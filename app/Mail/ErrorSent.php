<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ErrorSent extends Mailable
{
     use Queueable, SerializesModels;

    public $bill;

    public function __construct($bill)
    {
        $this->bill = $bill;
    }

    public function build()
    {
        return $this
            ->subject("Votre facture n° {$this->bill->no_bill}")
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->markdown('emails.invoices.send2')
            ->with([
                'bill'    => $this->bill,
                'company' => $this->bill->company,
            ]);
    }
}
