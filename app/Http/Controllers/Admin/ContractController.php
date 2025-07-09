<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Company;
use App\Models\Owner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Barryvdh\Snappy\Facades\SnappyPdf as Pdf;

class ContractController extends Controller
{
    public function index() {
        return view('admin.contract.index');
    }

    public function annuals() {
        return view('admin.contract.annual-index');
    }

    public function create(Company $company) {
        return view('admin.contract.create', compact('company'));
    }

    public function edit(Contract $contract) {
        return view('admin.contract.edit', compact('contract'));
    }

    public function preview($company, $period, $contractIds)
    {
        $previewUrl = route('admin.contracts.pdf.calculate.preview', [
            'company' => $company,
            'period' => $period,
            'contracts' => $contractIds,
        ]);

        $period_bills = Carbon::createFromFormat('d-m-Y', substr($period, 0, 10))->format('m-Y');
        $dateStart = Carbon::createFromFormat('d-m-Y', substr($period, 0, 10))->startOfMonth()->format('d/m/Y');
        $date = Carbon::createFromFormat('d-m-Y', substr($period, 0, 10))->startOfMonth();

        $filename = "BRO-2025-{$company}-{$period_bills}.pdf";
        $path = "private/contracts/{$period_bills}/{$filename}";

        $contractIds = explode('-', $contractIds);

        $contracts = Contract::with([
            'type_period',
            'company.city',
            'contract_product_detail.type_product.type_contract'
        ])
        ->whereIn('id', $contractIds)
        ->get();

        $contract = $contracts->first();
        $owner = Owner::first();
        $vatResumes = $this->getVatResumesFromContracts($contracts, $date);
        $totals = $this->getTotalsFromVatResumes($vatResumes);
        $bill = null;
        $products = collect();
        foreach ($contracts as $contract) {
            foreach ($contract->contract_product_detail as $product) {
                $product->contract = $contract;
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
            'bill'
            ))
            ->setOption('enable-local-file-access', true)
            ->setOption('margin-top', 10)
            ->setOption('margin-right', 8)
            ->setOption('margin-left', 8)
            ->setOption('margin-bottom', 5);

        Storage::put($path, $pdf->output());

        $fullPath = storage_path("app/{$path}");

        $response = response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
        ]);

        $response->setContentDisposition('inline', $filename);

        return $response;
    }

    public function getVatResumesFromContracts($contracts, $date = null)
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

    public function getTotalsFromVatResumes($vatResumes)
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
