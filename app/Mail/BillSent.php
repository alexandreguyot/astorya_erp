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
        $fileName = "{$this->bill->no_bill}.pdf";
        $baseDir  = storage_path('app');

        /**
         * 1️⃣ PRIORITÉ ABSOLUE : file_path en base
         */
        if (!empty($this->bill->file_path)) {
            $directPath = $baseDir . '/' . ltrim($this->bill->file_path, '/');

            if (is_file($directPath)) {
                Log::info('BillSent: PDF trouvé via file_path', [
                    'path' => $directPath,
                ]);

                return $directPath;
            }

            Log::warning('BillSent: file_path présent mais fichier introuvable', [
                'file_path' => $this->bill->file_path,
                'resolved'  => $directPath,
            ]);
        }

        /**
         * 2️⃣ FALLBACK : recherche par generated_at (ancien comportement)
         */
        $dt = $this->bill->generated_at instanceof Carbon
            ? $this->bill->generated_at->copy()
            : Carbon::createFromFormat('d/m/Y', (string) $this->bill->generated_at);

        $facturesDir = $baseDir . '/private/factures';

        $candidates = [
            $dt->format('Y-m'),
            $dt->copy()->subMonth()->format('Y-m'),
            $dt->copy()->addMonthsNoOverflow()->format('Y-m'),
        ];

        foreach ($candidates as $ym) {
            $path = "{$facturesDir}/{$ym}/{$fileName}";
            if (is_file($path)) {
                Log::info('BillSent: PDF trouvé via fallback', [
                    'path' => $path,
                    'fallback_month' => $ym,
                ]);

                return $path;
            }
        }

        /**
         * 3️⃣ ÉCHEC TOTAL
         */
        Log::error('BillSent: PDF introuvable (file_path + fallback)', [
            'bill'        => $this->bill->no_bill,
            'file_path'  => $this->bill->file_path,
            'generated'  => (string) $this->bill->generated_at,
            'searched'   => $candidates,
        ]);

        throw new \RuntimeException(
            "PDF de la facture {$this->bill->no_bill} introuvable (file_path et fallback échoués)."
        );
    }
}
