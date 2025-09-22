<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Bill;
use App\Jobs\GenerateBillPdf;

class RegenerateBillsByGeneratedDate extends Command
{
    protected $signature = 'bills:regen-date
                            {--date= : Date au format YYYY-MM-DD (sur le champ generated_at)}
                            {--dry : Simulation, ne touche pas aux fichiers}';

    protected $description = 'Régénère les PDF des factures dont generated_at correspond à une date donnée, en sauvegardant les anciens PDF dans backup/.';

    public function handle()
    {
        $dateOpt = $this->option('date');
        $dry     = (bool) $this->option('dry');

        if (!$dateOpt) {
            $this->error("Option --date requise (ex: --date=2025-09-11).");
            return self::FAILURE;
        }

        try {
            $targetDate = Carbon::createFromFormat('Y-m-d', $dateOpt)->startOfDay();
        } catch (\Throwable $e) {
            $this->error("Format de date invalide. Utilise YYYY-MM-DD (ex: 2025-09-11).");
            return self::FAILURE;
        }

        $query = Bill::query()->whereDate('generated_at', $dateOpt);

        $count = $query->count();
        if ($count === 0) {
            $this->warn("Aucune facture trouvée avec generated_at = {$targetDate->toDateString()}.");
            return self::SUCCESS;
        }

        $this->info("{$count} facture(s) trouvée(s) pour le {$targetDate->toDateString()}.");
        if ($dry) {
            $this->warn("Mode --dry activé : aucune écriture disque ni régénération effective.");
        }

        $ok = 0; $moved = 0; $failed = 0;

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->chunk(50, function ($bills) use ($bar, $dry, &$ok, &$moved, &$failed, $dateOpt) {
            foreach ($bills as $bill) {
                try {
                    // Dossier/fichier exacts attendus
                    $folderYm = Carbon::parse($bill->generated_at)->format('Y-m');
                    $filename = rtrim($bill->no_bill, '.pdf') . '.pdf';

                    // Si ton modèle stocke déjà le chemin complet, on le respecte,
                    // sinon on reconstruit tel que souhaité.
                    $privatePath = $bill->file_path ?: "private/factures/{$folderYm}/{$filename}";

                    // Dossiers à préparer
                    $targetDir  = dirname($privatePath);
                    $backupBase = 'private/factures/backup/' . now()->format('Y-m-d');
                    $backupPath = "{$backupBase}/{$filename}";

                    if (!$dry) {
                        Storage::disk('local')->makeDirectory($targetDir);
                        Storage::disk('local')->makeDirectory($backupBase);
                    }

                    // Déplacement en backup si le PDF existe
                    if (Storage::disk('local')->exists($privatePath)) {
                        if (!$dry) {
                            if (Storage::disk('local')->exists($backupPath)) {
                                $nameOnly = pathinfo($filename, PATHINFO_FILENAME);
                                $ext      = pathinfo($filename, PATHINFO_EXTENSION);
                                $backupPath = "{$backupBase}/{$nameOnly}-".Str::random(6).".{$ext}";
                            }
                            Storage::disk('local')->move($privatePath, $backupPath);
                            $moved++;
                        }
                    }

                    // --- Régénération FORCÉE, en direct (pas de queue) ---
                    if (!$dry) {
                        // Appel direct du Job = sûr et synchrone
                        (new GenerateBillPdf($bill, $dateOpt))->handle();
                    }

                    // Vérification post-génération
                    $generated = !$dry && Storage::disk('local')->exists($privatePath);

                    if ($generated || $dry) {
                        $ok++;
                    } else {
                        $failed++;
                        $this->warn("\n⚠️  Échec de génération pour bill #{$bill->id} ({$filename})");
                    }

                } catch (\Throwable $ex) {
                    $failed++;
                    $this->warn("\n❌ Erreur pour bill #{$bill->id} : ".$ex->getMessage());
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Terminé.");
        $this->line("• Déplacés en backup : {$moved}");
        $this->line("• OK                 : {$ok}");
        if ($failed > 0) {
            $this->error("• ÉCHECS             : {$failed} (voir messages ci-dessus)");
        }
        if ($dry) {
            $this->comment("ℹ️ Rappel : --dry n’a rien modifié. Relance sans --dry pour exécuter réellement.");
        }

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
