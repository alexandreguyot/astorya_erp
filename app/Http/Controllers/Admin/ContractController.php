<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Company;
use App\Models\Owner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Barryvdh\Snappy\Facades\SnappyPdf as Pdf;
use Spatie\Browsershot\Browsershot;

class ContractController extends Controller
{
    public function index() {
        return view('admin.contract.index');
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
        $css = null;

        $pdf = Pdf::loadView('pdf.bills', compact(
            'contract',
            'contracts',
            'products',
            'dateStart',
            'owner',
            'vatResumes',
            'totals',
            'bill',
            'css'
            ))
            ->setOption('enable-local-file-access', true)
            ->setOption('margin-top', 10)
            ->setOption('margin-right', 10)
            ->setOption('margin-bottom', 5)
            ->setOption('margin-left', 10);

        Storage::put($path, $pdf->output());

        return response()->file(storage_path("app/{$path}"));
    }

    public function preview_spatieBrowser($company, $period, $contractIds)
    {
        $previewUrl = route('admin.contracts.pdf.calculate.preview',
            [
                'company' => $company,
                'period' => $period,
                'contracts' => $contractIds,
            ]
        );

        // 2) Lancer une première passe pour mesurer header/footer
        $jsonDimensions = Browsershot::url($previewUrl)
            ->noSandbox()                   // nécessaire en Docker/Sail
            ->waitUntilNetworkIdle()        // attendre que tout soit chargé
            ->evaluate(<<<'JS'
                () => {
                // on retourne un POJO simple
                const h = document.querySelector('#header')?.getBoundingClientRect() || {};
                const c = document.querySelector('#content')?.getBoundingClientRect() || {};
                const f = document.querySelector('#footer')?.getBoundingClientRect() || {};
                return JSON.stringify({
                    headerHeight: h.height || 0,
                    footerHeight: f.height || 0
                });
                }
            JS
            );

        $dims = json_decode($jsonDimensions, true);

        // 3) Convertir les pixels en millimètres (1px ≃ 0.264583 mm à 96 DPI)
        $pxToMm = fn($px) => round($px * 0.264583, 2);
        $marginTop    = $pxToMm($dims['headerHeight']);
        $marginBottom = $pxToMm($dims['footerHeight']);

        // 4) Générer le PDF avec ces marges dynamiques
        $periodSlug = Carbon::createFromFormat('d-m-Y', substr($period, 0, 10))->format('m-Y');
        $filename   = "BRO-2025-{$company}-{$periodSlug}.pdf";
        $outPath    = storage_path("app/private/contracts/{$periodSlug}/{$filename}");
        if (!is_dir(dirname($outPath))) {
            mkdir(dirname($outPath), 0755, true);
        }

        Browsershot::url($previewUrl)
            ->noSandbox()
            ->showBackground()     // pour imprimer les backgrounds CSS
            ->format('A4')
            ->margins($marginTop, 10, $marginBottom, 10)  // top, right, bottom, left en mm
            ->save($outPath);

        // 5) Retourner le PDF au navigateur
        return response()->file($outPath);
    }


    public function previewHtml($company, $period, $contractIds)
    {
        $dateStart = Carbon::createFromFormat('d-m-Y', substr($period, 0, 10))->startOfMonth()->format('d/m/Y');

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
        $vatResumes = $this->getVatResumesFromContracts($contracts);
        $totals = $this->getTotalsFromVatResumes($vatResumes);
        $bill = null;
        $products = collect();
        foreach ($contracts as $contract) {
            foreach ($contract->contract_product_detail as $product) {
                $product->contract = $contract; // On garde le contrat pour accès dans la vue
                $products->push($product);
            }
        }

        $css = file_get_contents(public_path('css/pdf.css'));

       return view('pdf.bills', compact(
            'contract',
            'contracts',
            'products',
            'dateStart',
            'owner',
            'vatResumes',
            'totals',
            'bill',
            'css',
       ));
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
