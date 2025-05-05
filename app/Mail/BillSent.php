<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BillSent extends Mailable
{
    use Queueable, SerializesModels;

    public $bill;

    public function __construct($bill)
    {
        $this->bill = $bill;
    }

    public function build()
    {
        $pdfPath = storage_path("app/private/factures/"
            . now()->createFromFormat('d/m/Y', $this->bill->generated_at)->format('m-Y')
            . "/{$this->bill->no_bill}.pdf"
        );

        return $this
            ->subject("Votre facture n° {$this->bill->no_bill}")
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->markdown('emails.invoices.send')
            ->attach($pdfPath, [
                'as'   => "{$this->bill->no_bill}.pdf",
                'mime' => 'application/pdf',
            ])
            ->with([
                'bill'    => $this->bill,
                'company' => $this->bill->company,
            ]);
    }
}
