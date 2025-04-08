<?php

namespace App\Console\Commands;

use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Console\Command;

class ImportProducts extends Command
{
    protected $signature = 'import:products';
    protected $description = 'Import products from an Excel file';

    public function handle()
    {
        $path = base_path('imports/articles.csv');

        Excel::import(new ProductImport, $path);

        $this->info('Products imported successfully!');
    }
}

