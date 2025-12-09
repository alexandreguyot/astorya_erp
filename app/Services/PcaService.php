<?php

namespace App\Services;

use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PcaService
{
    public function getPcaLinesForYear(int $year): Collection
    {
        $start        = Carbon::create($year, 10, 1);
        $end          = Carbon::create($year, 12, 31);
        $nextYearStart = Carbon::create($year + 1, 1, 1); // 01/01/YYYY+1

        // On récupère toutes les lignes de facture de la période
        $billsGrouped = Bill::with([
                'company',
                'contract.contract_product_detail.type_product',
            ])
            ->whereBetween('started_at', [$start, $end])
            ->get()
            ->groupBy('no_bill'); // ← groupement par numéro de facture

        $result = collect();

        foreach ($billsGrouped as $noBill => $billLines) {

            foreach ($billLines as $bill) {

                // Dates dans le format du projet
                $periodStart = Carbon::createFromFormat(config('project.date_format'), $bill->started_at);
                $periodEnd   = Carbon::createFromFormat(config('project.date_format'), $bill->billed_at);

                // ❌ Exclure si billed_at est AVANT le 01/01/YYYY+1
                if ($periodEnd->lt($nextYearStart)) {
                    continue;
                }
                // Total des jours de la période facturée (inclusif)
                $totalDays = $periodEnd->diffInDays($periodStart) + 1;

                // Jours à proratiser = jours après la nouvelle année (inclusif)
                $prorataDays = $periodEnd->diffInDays($nextYearStart) + 1;

                // Montant de LA LIGNE de facture (ce que tu veux utiliser)
                $amount = $bill->amount_vat_included;

                // On va chercher un produit lié au contrat
                $contract = $bill->contract;
                $detail   = $contract?->contract_product_detail->first(); // on prend le premier détail
                $product  = $detail?->type_product;

                $pca = $totalDays > 0
                    ? ($amount * $prorataDays / $totalDays)
                    : 0;

                $result->push([
                    'Facture'          => $noBill,
                    'Client'           => $bill->company?->name,
                    'Montant HT'       => number_format($amount, 2, ',', ''), // tu peux renommer si c’est TTC
                    'Début'            => $periodStart->format('d/m/Y'),
                    'Fin'              => $periodEnd->format('d/m/Y'),
                    'Nombre de jours'  => $totalDays,
                    'Jours proratisés' => $prorataDays,
                    'Montant PCA'      => number_format($pca, 2, ',', ''),
                    'Service'          => $product?->designation_short ?? '',
                    'Compte'           => $product?->accounting ?? '',
                ]);
            }
        }

        return $result;
    }
}
