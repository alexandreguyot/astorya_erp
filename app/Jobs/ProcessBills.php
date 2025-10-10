<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\Contract;
use App\Actions\GenerateAccountingHisto;
use App\Jobs\GenerateBillPdf;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Throwable;
use Illuminate\Support\Facades\Log;

class ProcessBills implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $companyName;
    public $contractIds;
    public $startedAt;
    public $billedAt;
    public $userId;
    public string $groupKey;

    public function __construct($companyName, $contractIds, $startedAt, $billedAt, $userId, string $groupKey)
    {
        $this->companyName = $companyName;
        $this->contractIds = $contractIds;
        $this->startedAt = $startedAt;
        $this->billedAt = $billedAt;
        $this->userId = $userId;
        $this->groupKey = $groupKey;
    }

    public function handle()
    {
        $dateStart = Carbon::createFromFormat(config('project.date_format'), $this->startedAt)->startOfMonth();
        $contracts = Contract::with([
            'type_period',
            'company.city',
            'contract_product_detail' => function ($q) use ($dateStart) {
                $q->whereNull('billing_terminated_at')
                    ->orWhereDate('billing_terminated_at', '0001-01-01')
                    ->orWhereDate('billing_terminated_at', '>=', $dateStart);
            },
            'contract_product_detail.type_product.type_contract',
            'contract_product_detail.type_product.type_vat',
        ])->whereIn('id', $this->contractIds)->get();

        if ($contracts->isEmpty()) {
            return;
        }
        $noBill = Bill::getBillNumber();

        $dateStarted = Carbon::createFromFormat(config('project.date_format'), $this->startedAt);

        DB::transaction(function () use ($contracts, $noBill, $dateStarted) {
            foreach ($contracts as $contract) {

                $alreadyExists = Bill::where('contract_id', $contract->id)
                ->whereNot('no_bill', 'like', 'BRO-%')
                ->where('started_at', $dateStarted->format('Y-m-d'))
                ->exists();

                if ($alreadyExists) {
                    Log::info("ProcessBills[$noBill] skip (already billed) contract={$contract->id}");
                    continue;
                }

                $exists = Bill::where('contract_id', $contract->id)
                            ->whereNot('no_bill', 'like', 'BRO-%')
                          ->where('started_at', $dateStarted->format('Y-m-d'))
                          ->exists();

                Log::debug("ProcessBills[{$noBill}] checking contract {$contract->id} for {$this->startedAt}");
                if ($exists) {
                    Log::info("ProcessBills[{$noBill}] skipping already billed contract {$contract->id}");
                    continue;
                }

                // ➜ Compte les détails actifs pour ce mois via le scope (source de vérité)
                $activeCount = $contract->contract_product_detail()
                    ->activeAt($dateStarted)
                    ->count();

                if ($activeCount === 0) {
                    Log::warning("ProcessBills[$noBill] skip (no active details) contract={$contract->id} company={$contract->company->name}");
                    continue;
                }

                // ➜ Calcule les montants sur la base des détails actifs
                $amountHt  = $contract->calculateTotalPrice($dateStarted);
                $amountTtc = $contract->calculateTotalPriceWithVat($dateStarted);


                if ($amountHt <= 0) {
                    Log::warning("ProcessBills[$noBill] skip (zero amount) contract={$contract->id} company={$contract->company->name}");
                    continue;
                }

                Log::info("ProcessBills[{$noBill}] creating bill for contract {$contract->id}");

                $bill = new Bill();
                $bill->company_id = $contracts->first()->company_id;
                $bill->no_bill = $noBill;
                $bill->generated_at = now()->format(config('project.date_format'));
                $bill->validated_at = now()->format(config('project.date_format'));
                $bill->started_at = $this->startedAt;
                $bill->billed_at = $this->billedAt;
                $bill->amount = $amountHt;
                $bill->amount_vat_included  = $amountTtc;
                $bill->type_period_id = $contract->type_period_id;
                $bill->contract_id = $contract->id;

                if ($bill->save()) {
                    $contract->billed_at = now()->format(config('project.date_format'));
                    $contract->save();
                    $lastEndRaw = $bill->getRawOriginal('billed_at') ?? $bill->billed_at;
                    $lastEnd = \Carbon\Carbon::parse($lastEndRaw)->format('Y-m-d');

                    foreach ($contract->contract_product_detail as $detail) {
                        $detail->last_billed_at = $lastEnd;
                        $detail->save();
                    }
                }
                Log::info("ProcessBills[{$noBill}] bill created, id={$bill->id}");
            }
        });

       $bills = Bill::with([
            'contract.company',
            'contract.contract_product_detail' => function ($q) use ($dateStart) {
                $q->whereNull('billing_terminated_at')
                    ->orWhereDate('billing_terminated_at', '0001-01-01')
                    ->orWhereDate('billing_terminated_at', '>=', $dateStart);
                },
            'contract.contract_product_detail.type_product.type_vat',
        ])
        ->where('no_bill', $noBill)
        ->get();

        app(GenerateAccountingHisto::class)
            ->handleCollection($bills, $dateStarted);

        dispatch(new GenerateBillPdf($noBill, $dateStarted));

        $user = User::find($this->userId);
        if ($user) {
            $user->notify(new \App\Notifications\BillGenerationCompletedNotification($this->companyName, $noBill));
        }
        Cache::forget("processing.{$this->groupKey}");
        event(new \App\Events\NotificationsUpdated);
    }

    /**
     * Méthode de nettoyage commune à handle() et failed()
     */
    protected function cleanup(): void
    {
        Cache::forget("processing.{$this->groupKey}");
    }

    /**
     * Appelé automatiquement par Laravel si le job lève une exception.
     */
    public function failed(Throwable $exception)
    {
        $this->cleanup();

        Log::error("ProcessBills failed for group {$this->groupKey}", [
            'exception' => $exception,
        ]);
    }
}


