<?php

namespace App\Jobs;

use App\Models\Bill;
use App\Models\Owner;
use Barryvdh\Snappy\Facades\SnappyPdf as Pdf;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Bus\Dispatchable;
class GenerateBillPdf implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;
    use Dispatchable;

    public string $no_bill;
    public $dateStarted;
    public string $previewUrl;

    public function __construct(string $no_bill, $dateStarted)
    {
        $this->no_bill = $no_bill;
        $this->dateStarted = $dateStarted;
    }

    public function handle(): void
    {
        Log::info('Generating PDF for bill: ' . $this->no_bill);

        $bills = Bill::with([
            'contract',
            'contract.type_period',
            'type_period',
            'company.city',
            'company.bank_account',
            'contract.contract_product_detail' => function ($q) {
                $q->whereNull('billing_terminated_at')
                    ->orWhereDate('billing_terminated_at', '0001-01-01')
                    ->orWhereDate('billing_terminated_at', '>=', $this->dateStarted);
            },
            'contract.contract_product_detail.type_product.type_contract',
            'contract.contract_product_detail.type_product.type_vat',
        ])
        ->where('no_bill', $this->no_bill)
        ->get();

        if ($bills->isEmpty()) return;

        $filename = $bills->first()->no_bill . '.pdf';
        $period_bills = Carbon::createFromFormat('d/m/Y', $bills->first()->started_at)->format('Y-m');
        $dateStart = Carbon::createFromFormat('d/m/Y', $bills->first()->started_at)->format('d/m/Y');
        $date = Carbon::createFromFormat('d/m/Y', $bills->first()->started_at)->startOfMonth();

        $path = "private/factures/{$period_bills}/{$filename}";

        Log::info('PDF path: ' . $path);

        $contracts = collect();
        foreach ($bills as $bill) {
            $contracts->push($bill->contract);

            $bill->update(['file_path' => $path]);
            $bill->save();
        }

        $contract = $bills->first()->contract;
        $owner = Owner::first();

        $vatResumes = $this->getVatResumesFromContracts($contracts, $date);
        $totals = $this->getTotalsFromVatResumes($vatResumes);

        $products = collect();
        foreach ($contracts as $contract) {
            foreach ($contract->contract_product_detail as $product) {
                $products->push($product);
            }
        }

        $pdf = Pdf::loadView('pdf.bills', compact(
            'contract',
            'contracts',
            'products',
            'dateStart',
            'owner',
            'vatResumes',
            'totals',
            'bill',
        ))
        ->setOption('enable-local-file-access', true)
        ->setOption('margin-top', 10)
        ->setOption('margin-right', 10)
        ->setOption('margin-bottom', 5)
        ->setOption('margin-left', 10);

        Storage::put($path, $pdf->output());

        Log::info('PDF Generated and saved for bill: ' . $this->no_bill);
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
