<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Illuminate\Support\Facades\Log;
use Swift_Plugins_LoggerPlugin;
use Swift_Plugins_Loggers_ArrayLogger;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Bill;

class ErrorSentEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $bill;

    public $tries = 3;
    public $backoff = [60, 120, 300];

    public function __construct($bill)
    {
        $this->bill = $bill;
    }

    public function middleware(): array
    {
        return [ new RateLimited('send-mails') ];
    }

    public function handle()
    {
        $company = $this->bill->company;
        $contact = $company->contact;
        if ($contact && $contact->email) {
            $toEmail = $contact->email;
            $toName  = "{$contact->firstname} {$contact->lastname}";
        } else {
            $toEmail = $company->email;
            $toName  = $company->name;
        }


        Mail::to($toEmail, $toName)
            ->send(new \App\Mail\ErrorSent($this->bill));
    }

    public function failed(Throwable $exception)
    {
        Log::error("SendBillEmail failed for group {$this->bill->no_bill}", [
            'exception' => $exception,
        ]);
    }
}
