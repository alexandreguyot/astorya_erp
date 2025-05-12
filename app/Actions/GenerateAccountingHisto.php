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
    public function handleCollection(Collection $bills): Collection
    {
        $entries = collect();

        foreach ($bills as $bill) {
            $label    = "{$bill->company->name} {$bill->no_bill}";
            $date     = Carbon::createFromFormat('d/m/Y', $bill->generated_at)
                                ->format(config('project.date_format'));
            $deadline = Carbon::createFromFormat('d/m/Y', $bill->generated_at)
                                ->addDays(7)
                                ->format(config('project.date_format'));
            $dateStart = Carbon::createFromFormat('d/m/Y', $bill->generated_at)->startOfMonth();

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

            $vatResumes = app()
                ->call('App\Http\Controllers\Admin\BillController@getVatResumesFromContracts', [
                    'contracts' => collect([$bill->contract]), 'date' => $dateStart
                ]);
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
}
