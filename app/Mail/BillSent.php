<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
        $pdfPath = $this->resolvePdfPath();

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

    /**
     * Détermine le bon chemin du PDF :
     * 1) mois de generated_at
     * 2) mois précédent
     * 3) (optionnel) mois suivant
     */
    protected function resolvePdfPath(): string
    {
        // $generatedAt peut être une string "d/m/Y" ou déjà un Carbon/DateTime
        $dt = $this->bill->generated_at instanceof Carbon
            ? $this->bill->generated_at->copy()
            : Carbon::createFromFormat('d/m/Y', (string)$this->bill->generated_at);

        $baseDir = storage_path('app/private/factures');
        $fileName = "{$this->bill->no_bill}.pdf";

        $candidates = [
            $dt->format('Y-m'),
            $dt->copy()->subMonth()->format('Y-m'),
            $dt->copy()->addMonth()->format('Y-m'), // <- garde-le si tu veux couvrir tous les cas
        ];

        foreach ($candidates as $ym) {
            $path = "{$baseDir}/{$ym}/{$fileName}";
            if (is_file($path)) {
                // Log facultatif pour savoir lequel a servi
                Log::info("BillSent: PDF trouvé", ['path' => $path]);
                return $path;
            }
        }

        // Rien trouvé : on log + on remonte une erreur explicite
        Log::warning("BillSent: PDF introuvable", [
            'bill' => $this->bill->no_bill,
            'generated_at' => (string)$this->bill->generated_at,
            'searched_in' => $candidates,
        ]);

        throw new \RuntimeException(
            "PDF de la facture {$this->bill->no_bill} introuvable dans: " . implode(', ', $candidates)
        );
    }
}
