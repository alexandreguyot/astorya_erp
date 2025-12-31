<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Owner;
use Barryvdh\Snappy\Facades\SnappyPdf as Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BillPdfService
{
    public function regenerate(string $noBill): void
    {
        Log::info("Regenerating PDF for bill {$noBill}");

        $bills = Bill::with([
            'contract',
            'contract.type_period',
            'type_period',
            'company.city',
            'company.bank_account',
            'contract.contract_product_detail.type_product.type_contract',
            'contract.contract_product_detail.type_product.type_vat',
        ])
        ->where('no_bill', $noBill)
        ->get();

        if ($bills->isEmpty()) {
            Log::warning("No bill found for {$noBill}");
            return;
        }

        $bill = $bills->first();

        // -------------------------
        // Dates & chemin
        // -------------------------
        $startedAt = Carbon::createFromFormat('d/m/Y', $bill->started_at);
        $periodYm  = $startedAt->format('Y-m');
        $dateStart = $startedAt->format('d/m/Y');

        $filename = "{$bill->no_bill}.pdf";
        $path     = "private/factures/{$periodYm}/{$filename}";

        // -------------------------
        // Contrats & produits
        // -------------------------
        $contracts = $bills->pluck('contract')->unique('id');
        $products  = $contracts
            ->flatMap(fn ($c) => $c->contract_product_detail);

        // -------------------------
        // TVA & totaux
        // -------------------------
        $owner = Owner::first();

        $vatResumes = $this->getVatResumesFromContracts($contracts, $startedAt);
        $totals     = $this->getTotalsFromVatResumes($vatResumes);

        // -------------------------
        // Génération PDF
        // -------------------------
        $pdf = Pdf::loadView('pdf.bills', compact(
            'contracts',
            'products',
            'dateStart',
            'owner',
            'vatResumes',
            'totals',
            'bill'
        ))
        ->setOption('enable-local-file-access', true)
        ->setOption('margin-top', 10)
        ->setOption('margin-right', 10)
        ->setOption('margin-bottom', 5)
        ->setOption('margin-left', 10);

        Storage::put($path, $pdf->output());

        // -------------------------
        // Mise à jour file_path
        // -------------------------
        Bill::where('no_bill', $noBill)->update([
            'file_path' => $path,
        ]);

        Log::info("PDF regenerated for bill {$noBill}", ['path' => $path]);
    }

    /* === Méthodes TVA / Totaux : reprises à l’identique === */

    protected function getVatResumesFromContracts($contracts, $date)
    {
        $vatResumes = [];

        foreach ($contracts as $contract) {
            foreach ($contract->contract_product_detail as $detail) {
                $vat = $detail->type_product->type_vat ?? null;
                if (!$vat) continue;

                $key = $vat->code_vat;

                $ht  = $detail->proratedBase($date);
                $tva = $detail->proratedWithVat($date) - $ht;

                if (!isset($vatResumes[$key])) {
                    $vatResumes[$key] = [
                        'code' => $vat->code_vat,
                        'account' => $vat->account_vat,
                        'percent' => $vat->percent,
                        'amount_ht' => 0,
                        'amount_tva' => 0,
                    ];
                }

                $vatResumes[$key]['amount_ht'] += $ht;
                $vatResumes[$key]['amount_tva'] += $tva;
            }
        }

        return collect($vatResumes)->map(fn ($item) => [
            'code' => $item['code'],
            'account' => $item['account'],
            'percent' => number_format($item['percent'], 2, ',', ' '),
            'amount_ht' => number_format($item['amount_ht'], 2, ',', ' '),
            'amount_tva' => number_format($item['amount_tva'], 2, ',', ' '),
        ]);
    }

    protected function getTotalsFromVatResumes($vatResumes)
    {
        $totalHt = 0;
        $totalTva = 0;

        foreach ($vatResumes as $item) {
            $ht  = (float) str_replace([' ', ','], ['', '.'], $item['amount_ht']);
            $tva = (float) str_replace([' ', ','], ['', '.'], $item['amount_tva']);
            $totalHt += $ht;
            $totalTva += $tva;
        }

        return [
            'total_ht'  => number_format($totalHt, 2, ',', ' '),
            'total_tva' => number_format($totalTva, 2, ',', ' '),
            'total_ttc' => number_format($totalHt + $totalTva, 2, ',', ' '),
        ];
    }
}
