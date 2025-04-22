<?php

namespace App\Console\Commands;

use App\Imports\CompanyImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Console\Command;

class ImportCompanies extends Command
{
    protected $signature = 'import:companies';
    protected $description = 'Import Companies from an Excel file';

    public function handle()
    {
        $path = base_path('imports/clients.csv');

        Excel::import(new CompanyImport, $path);

        $this->info('Companies imported successfully!');
    }
}

