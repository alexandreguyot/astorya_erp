<?php

// app/Console/Commands/ReorderForeignKeys.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReorderForeignKeys extends Command
{
    protected $signature = 'db:reorder-foreign-keys {table}';
    protected $description = 'Déplace toutes les colonnes *_id juste après id';

    public function handle()
    {
        $table = $this->argument('table');

        if (!Schema::hasTable($table)) {
            $this->error("❌ La table '$table' n'existe pas.");
            return;
        }

        $columns = DB::select("SHOW COLUMNS FROM `$table`");
        $columnNames = collect($columns)->pluck('Field')->toArray();

        if (!in_array('id', $columnNames)) {
            $this->error("❌ La table '$table' n'a pas de colonne 'id'.");
            return;
        }

        $foreignKeys = collect($columns)
            ->filter(fn($col) => str_ends_with($col->Field, '_id') && $col->Field !== 'id')
            ->mapWithKeys(fn($col) => [$col->Field => $col->Type])
            ->all();

        if (empty($foreignKeys)) {
            $this->info("✅ Aucune colonne *_id à déplacer.");
            return;
        }

        foreach ($foreignKeys as $column => $type) {
            $sql = "ALTER TABLE `$table` MODIFY `$column` $type AFTER `id`;";
            $this->info("➡️ $sql");
            try {
                DB::statement($sql);
                $this->info("✅ Colonne `$column` déplacée après `id`.");
            } catch (\Exception $e) {
                $this->error("❌ Échec pour `$column` : " . $e->getMessage());
            }
        }

        $this->info("🎉 Réorganisation terminée.");
    }
}


