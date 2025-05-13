<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Jobs\GenerateBillPdf;
use App\Models\Bill;

class GenerateHistoricalBills extends Command
{
    protected $signature   = 'bills:generate-historical {--from=2023-01} {--to=2025-04}';
    protected $description = 'Génère les factures de chaque mois entre deux périodes';

    public function handle()
    {
        $from = Carbon::createFromFormat('Y-m', $this->option('from'))->startOfMonth();
        $to   = Carbon::createFromFormat('Y-m', $this->option('to'))->startOfMonth();

        for ($dt = $from; $dt->lte($to); $dt->addMonth()) {
            $periodKey = $dt->format('Y-m');
            $dateStart = $dt->format('d/m/Y');
            $billedAt  = $dt->copy()->endOfMonth()->format('d/m/Y');

            $this->info("Génération des factures pour : $periodKey");

            $bills = Bill::where('generated_at', 'like', $periodKey.'-%')
                ->pluck('no_bill')->unique();

            if ($bills->isEmpty()) {
                $this->warn("Aucune facture trouvée pour la période : $periodKey");
                continue;
            }
            foreach ($bills as $no_bill) {
                $this->info("Génération de la facture : $no_bill");
                dispatch(new GenerateBillPdf($no_bill));
            }
            $this->info("Factures générées avec succès pour la période : $periodKey");

        }

        $this->info('Terminé.');
    }
}
