<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PcaExport implements FromCollection, WithHeadings
{
    public function __construct(private Collection $lines)
    {
    }

    public function collection()
    {
        return $this->lines;
    }

    public function headings(): array
    {
        return [
            'Facture',
            'Client',
            'Montant HT',
            'Début',
            'Fin',
            'Nombre de jours',
            'Nombre de jours à proratiser',
            'Montant PCA',
            'Service',
            'Compte',
        ];
    }
}
