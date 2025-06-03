<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Bill;

class ImportFacturesFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bills:import-files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parcourt le dossier import_factures, associe chaque fichier à la facture en base, puis déplace le fichier vers storage/app/private/factures/{YYYY-MM}/{filename}';

    public function handle()
    {
        // 1. On considère que les fichiers d’origine sont dans storage/app/import_factures
        //    (ou vous pouvez ajuster selon l’endroit où vous les placez réellement)
        $importDir = base_path('import_factures');

        if (! File::isDirectory($importDir)) {
            $this->error("Le dossier import_factures n'existe pas dans ({$importDir}).");
            return 1;
        }

        $files = File::files($importDir);

        if (empty($files)) {
            $this->info("Aucun fichier trouvé dans {$importDir}.");
            return 0;
        }

        foreach ($files as $file) {
            $filename      = $file->getFilename();
            $filenameNoExt = pathinfo($filename, PATHINFO_FILENAME);

            // 2. On cherche la facture correspondante en base selon le no_bill (nom du fichier sans extension)
            $bill = Bill::where('no_bill', $filenameNoExt)->first();

            if (! $bill) {
                $this->warn("Aucune facture trouvée pour le fichier : {$filename} (no_bill = {$filenameNoExt}).");
                Log::warning("ImportFacturesFiles : facture introuvable pour le fichier {$filename}");
                continue;
            }

            // 3. On parse la date generated_at, en partant du format configuré dans project.date_format
            try {
                $generatedAt = Carbon::createFromFormat(config('project.date_format'), $bill->generated_at);
            } catch (\Exception $e) {
                $this->error("Impossible de parser generated_at pour la facture {$bill->no_bill} ({$bill->generated_at}).");
                Log::error("ImportFacturesFiles : échec du parsing de generated_at pour no_bill {$bill->no_bill} - {$e->getMessage()}");
                continue;
            }

            // 4. On détermine la période "YYYY-MM"
            $period = $generatedAt->format('Y-m');

            // 5. Construire le chemin relatif voulu : "private/factures/{YYYY-MM}/{filename}"
            $relativeDir  = "private/factures/{$period}";
            $relativePath = "{$relativeDir}/{$filename}";

            // 6. Chemin physique complet dans storage/app :
            //    ex. /var/www/html/storage/app/private/factures/2024-01/FACT-2024-150.pdf
            $destinationDir  = storage_path("app/{$relativeDir}");
            $destinationPath = storage_path("app/{$relativePath}");

            // 7. Création du dossier destination s’il n’existe pas
            if (! File::isDirectory($destinationDir)) {
                File::makeDirectory($destinationDir, 0755, true);
            }

            // 8. Déplacement du fichier depuis import_factures → storage/app/private/factures/…
            try {
                File::move($file->getRealPath(), $destinationPath);
                $this->info("Fichier {$filename} déplacé vers storage/app/{$relativePath} pour la facture {$bill->no_bill}.");
                Log::info("ImportFacturesFiles : fichier {$filename} déplacé vers {$destinationPath}");

                // 9. On enregistre dans la base **uniquement** le chemin relatif
                $bill->file_path = $relativePath;
                $bill->save();
            } catch (\Exception $e) {
                $this->error("Erreur lors du déplacement de {$filename} vers {$destinationPath} : {$e->getMessage()}");
                Log::error("ImportFacturesFiles : échec du déplacement de {$filename} - {$e->getMessage()}");
            }
        }

        $this->info('Traitement terminé.');
        return 0;
    }
}
