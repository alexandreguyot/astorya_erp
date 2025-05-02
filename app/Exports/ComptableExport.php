<?php

namespace App\Exports;

use App\Models\Bill;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Date;

class ComptableExport implements FromCollection, WithHeadings
{
    protected $dateStart;
    protected $dateEnd;

    public function __construct($dateStart, $dateEnd)
    {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
    }

    public function collection()
    {
        $query = Bill::with(['company'])
            ->whereNotNull('no_bill')
            ->where('no_bill', 'like', 'FACT-%')
            ->when($this->dateStart && !$this->dateEnd, function ($query) {
                $dateStart = $this->convertDateFormat($this->dateStart, 'start');
                $query->where('generated_at', '>=', $dateStart);
            })
            ->when(!$this->dateStart && $this->dateEnd, function ($query) {
                $dateEnd = $this->convertDateFormat($this->dateEnd, 'end');
                $query->where('generated_at', '<=', $dateEnd);
            })
            ->when($this->dateStart && $this->dateEnd, function ($query) {
                $dateStart = $this->convertDateFormat($this->dateStart, 'start');
                $dateEnd = $this->convertDateFormat($this->dateEnd, 'end');
                $query->whereBetween('generated_at', [$dateStart, $dateEnd]);
            })
            ->orderBy('generated_at', 'asc')
            ->get();

        return $query->map(function ($bill) {
            return [
                'Journal' => 'VT',
                'Numéro facture' => $bill->no_bill ?? '',
                'Montant HT'     => $bill->amount ?? 0,
                'Date génération'=> $bill->generated_at ? $bill->generated_at->format('d/m/Y') : '',
            ];
        });
    }

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
            'Date d\'échéance',
            'Code mode de paiement',
        ];
    }

    protected function convertDateFormat($date, $boundary = null)
    {
        $parsed = \Carbon\Carbon::createFromFormat('d/m/Y', $date);

        if ($boundary === 'start') {
            return $parsed->startOfDay();
        }

        if ($boundary === 'end') {
            return $parsed->endOfDay();
        }

        return $parsed;
    }
}
