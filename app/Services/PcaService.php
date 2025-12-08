<?php

namespace App\Services;

use App\Models\Bill;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PcaService
{
    public function getPcaLinesForYear(int $year): Collection
    {
        $start = Carbon::create($year, 10, 1);
        $end   = Carbon::create($year, 12, 31);
        $limit = Carbon::create($year, 12, 31); // Limite année (31/12/YYYY)

        $bills = Bill::with(['company', 'contract.products'])
            ->whereBetween('started_at', [$start, $end])
            ->get();

        $result = collect();

        foreach ($bills as $bill) {

            $periodStart = Carbon::createFromFormat(config('project.date_format'), $bill->started_at);
            $periodEnd   = Carbon::createFromFormat(config('project.date_format'), $bill->billed_at);

            // ❌ 1. On NE GARDE que les factures dont la fin dépasse l'année
            if ($periodEnd->lte($limit)) {
                continue;
            }

            // 2. Nombre total de jours facturés
            $totalDays = $periodEnd->diffInDays($periodStart);

            // 3. Jours proratisés = jours après la nouvelle année
            $nextYearStart = Carbon::create($year + 1, 1, 1);
            $prorataDays = $periodEnd->diffInDays($nextYearStart);

            // 4. Montant HT de la ligne de facture
            $amountHT = $bill->amount_vat_included;
            // 5. Générer une ligne par produit du contrat
            foreach ($bill->contract->products as $product) {

                $pca = $totalDays > 0
                    ? ($amountHT * $prorataDays / $totalDays)
                    : 0;

                $result->push([
                    'Facture'              => $bill->no_bill,
                    'Client'               => $bill->company?->name,
                    'Montant HT'           => number_format($amountHT, 2, ',', ''),
                    'Début'                => $periodStart->format('d/m/Y'),
                    'Fin'                  => $periodEnd->format('d/m/Y'),
                    'Nombre de jours'      => $totalDays,
                    'Jours proratisés'     => $prorataDays,
                    'Montant PCA'          => number_format(round($pca, 10), 2, ',', ''),
                    'Service'              => $product->designation_short,
                    'Compte'               => $product->accounting,
                ]);
            }
        }

        return $result;
    }
}
