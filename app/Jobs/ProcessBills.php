<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\Contract;
use App\Jobs\GenerateBillPdf;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ProcessBills implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $companyName;
    public $contractIds;
    public $startedAt;
    public $billedAt;
    public $userId;

    public function __construct($companyName, $contractIds, $startedAt, $billedAt, $userId)
    {
        $this->companyName = $companyName;
        $this->contractIds = $contractIds;
        $this->startedAt = $startedAt;
        $this->billedAt = $billedAt;
        $this->userId = $userId;
    }

    public function handle()
    {
        $contracts = Contract::with([
            'type_period',
            'company.city',
            'contract_product_detail.type_product.type_contract',
            'contract_product_detail.type_product.type_vat',
        ])->whereIn('id', $this->contractIds)->get();

        if ($contracts->isEmpty()) {
            return;
        }

        $noBill = Bill::getBillNumber();

        DB::transaction(function () use ($contracts, $noBill) {
            foreach ($contracts as $contract) {
                $bill = new Bill();
                $bill->company_id = $contracts->first()->company_id;
                $bill->no_bill = $noBill;
                $bill->generated_at = now()->format(config('project.date_format'));
                $bill->started_at = $this->startedAt;
                $bill->billed_at = $this->billedAt;
                $bill->amount = str_replace(',', '.', $contract->total_price);
                $bill->amount_vat_included = str_replace(',', '.', $contract->total_price_with_vat);
                $bill->type_period_id = $contract->type_period_id;
                $bill->contract_id = $contract->id;

                if ($bill->save()) {
                    $contract->billed_at = now()->format(config('project.date_format'));
                    $contract->save();
                }
            }
        });

        dispatch(new GenerateBillPdf($noBill));

        // Envoie une notification à l'utilisateur
        $user = User::find($this->userId);
        if ($user) {
            $user->notify(new \App\Notifications\BillGenerationCompletedNotification($this->companyName, $noBill));
        }
    }
}


