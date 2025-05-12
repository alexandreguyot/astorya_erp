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
use Illuminate\Support\Facades\Config;
class GenerateBillPdf implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public string $no_bill;
    public string $previewUrl;

    public function __construct(string $no_bill)
    {
        $this->no_bill = $no_bill;
    }

    public function handle(): void
    {
        $bills = Bill::with([
            'contract',
            'contract.type_period',
            'type_period',
            'company.city',
            'company.bank_account',
            'contract.contract_product_detail.type_product.type_contract',
            'contract.contract_product_detail.type_product.type_vat',
        ])
        ->where('no_bill', $this->no_bill)
        ->get();

        if ($bills->isEmpty()) return;

        $filename = $bills->first()->no_bill . '.pdf';
        $period_bills = Carbon::createFromFormat('d/m/Y', $bills->first()->started_at)->format('m-Y');
        $dateStart = Carbon::createFromFormat('d/m/Y', $bills->first()->started_at)->format('d/m/Y');
        $date = Carbon::createFromFormat('d/m/Y', $bills->first()->started_at)->startOfMonth();

        $path = "private/factures/{$period_bills}/{$filename}";

        $contracts = collect();
        foreach ($bills as $bill) {
            $contracts->push($bill->contract);
        }

        $contract = $bills->first()->contract;
        $owner = Owner::first();
        $vatResumes = app()->call('App\Http\Controllers\Admin\BillController@getVatResumesFromContracts', ['contracts' => $contracts, 'date' => $date]);
        $totals = app()->call('App\Http\Controllers\Admin\BillController@getTotalsFromVatResumes', ['vatResumes' => $vatResumes]);

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
    }
}
