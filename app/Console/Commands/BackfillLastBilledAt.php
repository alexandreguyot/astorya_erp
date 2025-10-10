<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\ContractProductDetail;
use App\Models\Bill;

class BackfillLastBilledAt extends Command
{
    protected $signature = 'erp:backfill-last-billed
                            {--contract_id= : Limiter à un contrat}
                            {--detail_id=   : Limiter à un detail}
                            {--dry-run      : Affiche sans sauvegarder}';

    protected $description = 'Renseigne contract_product_details.last_billed_at depuis la dernière facture (billed_at) du contrat.';

    public function handle(): int
    {
        $details = ContractProductDetail::query()
            ->when($this->option('detail_id'),   fn($q)=>$q->where('id',$this->option('detail_id')))
            ->when($this->option('contract_id'), fn($q)=>$q->where('contract_id',$this->option('contract_id')))
            ->with('contract')
            ->get();

        if ($details->isEmpty()) {
            $this->info('Aucun detail trouvé.');
            return self::SUCCESS;
        }

        $dry = (bool) $this->option('dry-run');
        $updated = 0;

        foreach ($details as $d) {
            if (!$d->contract_id) {
                $this->line("Detail #{$d->id} : pas de contract_id → skip.");
                continue;
            }

            // Dernière facture du contrat, basée sur billed_at (fin de période)
            $bill = Bill::where('contract_id', $d->contract_id)
                ->whereNotNull('billed_at')
                ->orderByDesc('billed_at')
                ->first();

            if (!$bill) {
                $this->line("Detail #{$d->id} (contract {$d->contract_id}) : aucune facture → NULL.");
                continue;
            }

            // billed_at est formaté par un accessor → on veut la valeur brute (Y-m-d)
            $raw = $bill->getRawOriginal('billed_at') ?? $bill->billed_at;
            $lastEnd = Carbon::parse($raw)->endOfDay(); // on stocke la date, pas l’heure

            $currentRaw = $d->getRawOriginal('last_billed_at');
            $current = $currentRaw ? Carbon::parse($currentRaw) : null;

            if ($current && $current->gte($lastEnd)) {
                $this->line("Detail #{$d->id} : déjà >= {$lastEnd->toDateString()} → skip.");
                continue;
            }

            $this->info("Detail #{$d->id} : set last_billed_at = ".$lastEnd->toDateString().($dry?' [dry-run]':''));
            if (!$dry) {
                $d->last_billed_at = $lastEnd->format('Y-m-d');
                $d->save();
                $updated++;
            }
        }

        $this->info($dry ? 'Dry-run OK.' : "Backfill OK. {$updated} MAJ.");
        return self::SUCCESS;
    }
}
