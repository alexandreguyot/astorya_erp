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
        // Chemin du dossier contenant les fichiers à importer (à la racine du projet)
        $importDir = base_path('import_factures');

        if (! File::isDirectory($importDir)) {
            $this->error("Le dossier import_factures n'existe pas à la racine du projet ({$importDir}).");
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

            // On cherche la facture correspondante en base selon le no_bill
            $bill = Bill::where('no_bill', $filenameNoExt)->first();

            if (! $bill) {
                $this->warn("Aucune facture trouvée pour le fichier : {$filename} (no_bill = {$filenameNoExt}).");
                Log::warning("ImportFacturesFiles : facture introuvable pour le fichier {$filename}");
                continue;
            }

            // On récupère la date de génération de la facture
            try {
                $generatedAt = Carbon::createFromFormat(config('project.date_format'), $bill->generated_at);
            } catch (\Exception $e) {
                $this->error("Impossible de parser generated_at pour la facture {$bill->no_bill} ({$bill->generated_at}).");
                Log::error("ImportFacturesFiles : échec du parsing de generated_at pour no_bill {$bill->no_bill} - {$e->getMessage()}");
                continue;
            }

            // Période "YYYY-MM" pour organiser le dossier
            $period = $generatedAt->format('Y-m');

            // Chemin cible : storage/app/private/factures/{YYYY-MM}/{filename}
            $destinationDir  = storage_path("app/private/factures/{$period}");
            $destinationPath = "{$destinationDir}/{$filename}";

            // Création du dossier cible si nécessaire
            if (! File::isDirectory($destinationDir)) {
                File::makeDirectory($destinationDir, 0755, true);
            }

            // Déplacement du fichier
            try {
                File::move($file->getRealPath(), $destinationPath);
                $this->info("Fichier {$filename} déplacé vers {$destinationPath} pour la facture {$bill->no_bill}.");
                Log::info("ImportFacturesFiles : fichier {$filename} déplacé vers {$destinationPath}");
                $bill->update(['file_path' => $destinationPath]);
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
