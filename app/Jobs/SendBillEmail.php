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

class SendBillEmail implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $bill;

    public function __construct($bill)
    {
        $this->bill = $bill;
    }

    public function middleware(): array
    {
        // applique le rate limit “send-mails” défini plus haut
        return [ new RateLimited('send-mails') ];
    }

    public function handle()
    {
        $company = $this->bill->company;
        // déterminer destinataire prioritaire
        $contact = $company->contact;
        if ($contact && $contact->email) {
            $toEmail = $contact->email;
            $toName  = "{$contact->firstname} {$contact->lastname}";
        } else {
            $toEmail = $company->email;
            $toName  = $company->name;
        }

        Mail::to($toEmail, $toName)
            ->send(new \App\Mail\BillSent($this->bill));

        // mise à jour du modèle
        $this->bill->update(['sent_at' => Carbon::now()->format('d/m/Y')]);

        // suppression du flag
        Cache::forget("sending.bill.{$this->bill->no_bill}");
    }

    protected function cleanup(): void
    {
        Cache::forget("sending.bill.{$this->bill->no_bill}");
    }

    public function failed(Throwable $exception)
    {
        $this->cleanup();

        Log::error("SendBillEmail failed for group {$this->bill->no_bill}", [
            'exception' => $exception,
        ]);
    }
}
