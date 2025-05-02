<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class GenerateSelectedBills implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $selectedContracts;
    public $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($selectedContracts, $userId)
    {
        $this->selectedContracts = $selectedContracts;
        $this->userId           = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->selectedContracts as $selected) {
            $data = json_decode($selected, true);
            $started_at = substr($data['date'], 0, 10);
            $billed_at = substr($data['date'], 14, 14);
            dispatch(new ProcessBills($data['company'], implode('-', $data['contracts']), $started_at, $billed_at, $this->userId));
        }
    }
}
