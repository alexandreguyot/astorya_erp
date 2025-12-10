<?php

namespace App\Services;

use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PcaService
{
    public function getPcaLinesForYear(int $year): Collection
    {
        $start         = Carbon::create($year, 10, 1);
        $end           = Carbon::create($year, 12, 31);
        $nextYearStart = Carbon::create($year + 1, 1, 1); // 01/01/YYYY+1

        // On récupère toutes les lignes de facture de la période
        $billsGrouped = Bill::with([
                'company',
                'contract.contract_product_detail.type_product',
            ])
            ->whereBetween('started_at', [$start, $end])
            ->get()
            ->groupBy('no_bill'); // ← groupement par facture

        $result = collect();

        foreach ($billsGrouped as $noBill => $billLines) {
            foreach ($billLines as $bill) {

                // --- 1. Dates de période de la ligne ---
                // (on respecte ton config('project.date_format'))
                $periodStart = Carbon::createFromFormat(
                    config('project.date_format'),
                    $bill->started_at
                );
                $periodEnd = Carbon::createFromFormat(
                    config('project.date_format'),
                    $bill->billed_at
                );

                // On ne garde que les lignes qui dépassent l'année (PCA = jours après le 01/01/YYYY+1)
                if ($periodEnd->lt($nextYearStart)) {
                    continue;
                }

                // Total des jours de la période (inclusif)
                $totalDays = $periodEnd->diffInDays($periodStart) + 1;

                // Jours à proratiser = jours après la nouvelle année (inclusif)
                $prorataDays = $periodEnd->diffInDays($nextYearStart) + 1;

                if ($totalDays <= 0 || $prorataDays <= 0) {
                    continue;
                }

                // --- 2. Montant de la ligne de facture ---
                // Tu utilisais amount_vat_included dans ton dernier code
                $lineAmount = (float) $bill->amount_vat_included;

                $contract = $bill->contract;
                if (! $contract) {
                    continue;
                }

                $details = $contract->contract_product_detail ?? collect();
                if ($details->isEmpty()) {
                    continue;
                }

                // --- 3. Montant de base par produit (pour répartir le montant de la ligne) ---
                $detailsWithBase = $details->map(function ($detail) {
                    $base = (float) $detail->monthly_unit_price_without_taxe * (float) $detail->quantity;
                    $detail->base_amount_ht = $base;

                    return $detail;
                });

                $totalBase = $detailsWithBase->sum('base_amount_ht');

                // Si on ne sait pas répartir, on met tout sur le premier produit pour éviter de gonfler le total
                if ($totalBase <= 0) {
                    $detailsWithBase = collect([$detailsWithBase->first()]);
                    $detailsWithBase[0]->base_amount_ht = 1;
                    $totalBase = 1;
                }

                // --- 4. Une ligne PCA par produit lié au contrat ---
                foreach ($detailsWithBase as $detail) {
                    $product = $detail->type_product;
                    if (! $product) {
                        continue;
                    }

                    // Part du montant de facture pour ce produit (proportionnelle)
                    $weight       = $detail->base_amount_ht / $totalBase;
                    $productAmtHT = $lineAmount * $weight;

                    // Montant PCA pour ce produit
                    $pca = $productAmtHT * $prorataDays / $totalDays;

                    $result->push([
                        'Facture'          => $noBill,
                        'Client'           => $bill->company?->name,
                        'Montant HT'       => number_format($productAmtHT, 2, ',', ''),
                        'Début'            => $periodStart->format('d/m/Y'),
                        'Fin'              => $periodEnd->format('d/m/Y'),
                        'Nombre de jours'  => $totalDays,
                        'Jours proratisés' => $prorataDays,
                        'Montant PCA'      => number_format($pca, 2, ',', ''),
                        'Service'          => $product->designation_short ?? '',
                        'Compte'           => $product->accounting ?? '',
                    ]);
                }
            }
        }

        return $result;
    }
}
