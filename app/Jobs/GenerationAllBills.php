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
use App\Jobs\ProcessBills;

class GenerationAllBills implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $groupedContracts;

    public function __construct($groupedContracts, $userId)
    {
        $this->groupedContracts = $groupedContracts;
        $this->userId           = $userId;
    }
    
    public function handle(): void
    {
        $user = User::find($this->userId);
        foreach ($this->groupedContracts as $companyName => $periods) {
            foreach ($periods as $billingPeriod => $contracts) {

                $contractIds = $contracts->pluck('id')->toArray();

                $started_at = substr($billingPeriod, 0, 10);
                $billed_at = substr($billingPeriod, 14, 14);

                dispatch(new ProcessBills(
                    $companyName,
                    $contractIds,
                    $started_at,
                    $billed_at,
                    $user->id
                ));
            }
        }
    }
}
