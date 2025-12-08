<?php

namespace App\Http\Controllers;

use App\Exports\PcaExport;
use App\Services\PcaService;
use Maatwebsite\Excel\Facades\Excel;

class PcaExportController extends Controller
{
    public function export(int $year)
    {
        $lines = app(PcaService::class)->getPcaLinesForYear($year);

        return Excel::download(new PcaExport($lines), "PCA-$year.xlsx");
    }
}
