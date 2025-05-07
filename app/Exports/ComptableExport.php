<?php

namespace App\Exports;

use App\Models\AccountingHisto;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ComptableExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected string $start;
    protected string $end;

    /**
     * @param  string  $dateStart  format 'd/m/Y'
     * @param  string  $dateEnd    format 'd/m/Y'
     */
    public function __construct(string $dateStart, string $dateEnd)
    {
        $this->start = Carbon::createFromFormat('d/m/Y', $dateStart)
                              ->startOfDay()
                              ->format('Y-m-d');
        $this->end = Carbon::createFromFormat('d/m/Y', $dateEnd)
                              ->endOfDay()
                              ->format('Y-m-d');
    }

    public function collection()
    {
        dd($this->start, $this->end);
        return AccountingHisto::query()
            ->whereBetween('date', [$this->start, $this->end])
            ->orderByRaw("CAST(SUBSTRING_INDEX(no_bill, '-', -1) AS UNSIGNED) ASC")
            ->get();
    }

    /**
     * Les titres de colonnes dans l’ordre.
     */
    public function headings(): array
    {
        return [
            'Journal',
            'Date',
            'Numéro de pièce',
            'N° de compte',
            'Libellé',
            'Montant débit',
            'Montant crédit',
            "Date d'échéance",
            'Code mode de paiement',
        ];
    }

    /**
     * Conversion de chaque ligne AccountingHisto en tableau.
     */
    public function map($row): array
    {
        // On prend le raw DB pour avoir Y-m-d
        $rawDate     = $row->getRawOriginal('date');
        $rawDeadline = $row->getRawOriginal('deadline');

        return [
            $row->journal,
            Carbon::parse($rawDate)->format('d/m/Y'),
            $row->no_bill,
            $row->account_number,
            $row->label,
            number_format($row->debit_amount, 2, ',', ''),
            number_format($row->credit_amount, 2, ',', ''),
            Carbon::parse($rawDeadline)->format('d/m/Y'),
            $row->payment_code,
        ];
    }
}
