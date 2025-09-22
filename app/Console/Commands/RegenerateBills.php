<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bill;
use App\Jobs\GenerateBillPdf;

class RegenerateBills extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:regen {--year=} {--month=} {--day=} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Régénère les factures PDF (par année/mois ou toutes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $year  = $this->option('year');
        $month = $this->option('month');
        $month = $this->option('month');
        $all   = $this->option('all');

        $query = Bill::query();

        if ($all) {
            $this->info("Régénération de TOUTES les factures...");
        } elseif ($year && $month) {
            $this->info("Régénération des factures de {$month}/{$year}...");
            $query->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month);
        } elseif ($year) {
            $this->info("Régénération des factures de l'année {$year}...");
            $query->whereYear('created_at', $year);
        } else {
            $this->error("Tu dois utiliser --all ou bien --year (et éventuellement --month).");
            return Command::FAILURE;
        }

        $count = $query->count();
        if ($count === 0) {
            $this->warn("Aucune facture trouvée.");
            return Command::SUCCESS;
        }

        $this->info("{$count} facture(s) trouvée(s).");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->chunk(50, function ($bills) use ($bar) {
            foreach ($bills as $bill) {
                GenerateBillPdf::dispatchSync($bill);
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Régénération terminée !");

        return Command::SUCCESS;
    }
}
