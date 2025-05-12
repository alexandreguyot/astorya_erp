<?php

namespace App\Actions;

use App\Models\Bill;
use App\Models\AccountingHisto;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class GenerateAccountingHisto
{
    /**
     * Génère et persiste les écritures comptables pour une collection de factures.
     *
     * @param  Collection<Bill>  $bills
     * @return Collection<AccountingHisto>
     */
    public function handleCollection(Collection $bills, $dateStart): Collection
    {
        $entries = collect();

        foreach ($bills as $bill) {
            $label    = "{$bill->company->name} {$bill->no_bill}";
            $date     = Carbon::createFromFormat('d/m/Y', $bill->generated_at)
                                ->format(config('project.date_format'));
            $deadline = Carbon::createFromFormat('d/m/Y', $bill->generated_at)
                                ->addDays(7)
                                ->format(config('project.date_format'));

            foreach ($bill->contract->contract_product_detail as $detail) {
                $prod   = $detail->type_product;
                $amount = $detail->proratedBase($dateStart);

                $entries->push(AccountingHisto::create([
                    'journal'          => 'VT',
                    'date'             => $date,
                    'account_number'   => $prod->accounting,
                    'no_bill'          => $bill->no_bill,
                    'label'            => $label,
                    'deadline'         => $deadline,
                    'product_code'     => $prod->code,
                    'product_designation_short' => $prod->short_designation,
                    'company_name'     => $bill->company->name,
                    'company_accounting'       => $bill->company->accounting,
                    'company_ciel_reference'   => $bill->company->ciel_reference,
                    'debit_amount'     => 0,
                    'credit_amount'    => $this->normalizeDecimal($amount),
                ]));
            }

            $vatResumes = $this->getVatResumesFromContracts($bill->contract, $dateStart);

            foreach ($vatResumes as $vat) {
                $entries->push(AccountingHisto::create([
                    'journal'        => 'VT',
                    'date'           => $date,
                    'account_number' => $vat['account'],
                    'no_bill'        => $bill->no_bill,
                    'label'          => $label,
                    'deadline'       => $deadline,
                    'debit_amount'   => 0,
                    'credit_amount'  => $this->normalizeDecimal($vat['amount_tva']),
                ]));
            }

            $entries->push(AccountingHisto::create([
                'journal'        => 'VT',
                'date'           => $date,
                'account_number' => $bill->company->accounting,
                'no_bill'        => $bill->no_bill,
                'label'          => $label,
                'deadline'       => $deadline,
                'debit_amount'   => $this->normalizeDecimal($bill->amount_vat_included),
                'credit_amount'  => 0,
            ]));
        }

        return $entries;
    }

    protected function normalizeDecimal(mixed $value): float
    {
        // Si c'est une chaîne, on enlève d'abord les espaces (y compris les NBSP)
        $value = str_replace([' ', "\xc2\xa0"], '', $value);
        // Puis on transforme la virgule en point
        $value = str_replace(',', '.', $value);
        // Retourne un float, 0.0 si $value était null ou non numérique
        return (float) $value;
    }

    protected function getVatResumesFromContracts($contracts, $date = null)
    {
        $vatResumes = [];
        foreach ($contracts as $contract) {
            foreach ($contract->contract_product_detail as $detail) {
                $vat = $detail->type_product->type_vat ?? null;
                if (!$vat) continue;

                $key = $vat->code_vat;

                $ht = $detail->proratedBase($date);
                $tva = $detail->proratedWithVat($date) - $ht;

                if (!isset($vatResumes[$key])) {
                    $vatResumes[$key] = [
                        'code' => $vat->code_vat,
                        'account' => $vat->account_vat,
                        'percent' => $vat->percent,
                        'amount_ht' => 0,
                        'amount_tva' => 0,
                    ];
                }

                $vatResumes[$key]['amount_ht'] += $ht;
                $vatResumes[$key]['amount_tva'] += $tva;
            }
        }

        return collect($vatResumes)->map(function ($item) {
            return [
                'code' => $item['code'],
                'account' => $item['account'],
                'percent' => number_format($item['percent'], 2, ',', ' '),
                'amount_ht' => number_format($item['amount_ht'], 2, ',', ' '),
                'amount_tva' => number_format($item['amount_tva'], 2, ',', ' '),
            ];
        });
    }

    protected function getTotalsFromVatResumes($vatResumes)
    {
        $totalHt = 0;
        $totalTva = 0;

        foreach ($vatResumes as $item) {
            $ht = (float) str_replace([' ', ','], ['', '.'], $item['amount_ht']);
            $tva = (float) str_replace([' ', ','], ['', '.'], $item['amount_tva']);
            $totalHt += $ht;
            $totalTva += $tva;
        }

        return [
            'total_ht' => number_format($totalHt, 2, ',', ' '),
            'total_tva' => number_format($totalTva, 2, ',', ' '),
            'total_ttc' => number_format($totalHt + $totalTva, 2, ',', ' '),
        ];
    }
}
