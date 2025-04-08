<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Owner;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Barryvdh\Snappy\Facades\SnappyPdf as Pdf;

class ContractController extends Controller
{
    public function index() {
        return view('admin.contract.index');
    }

    public function create() {
        return view('admin.contract.create');
    }

    public function edit(Contract $contract) {
        return view('admin.contract.edit', compact('contract'));
    }

    public function pdf(Contract $contract) {
        $owner = Owner::first();

        // Logique de génération du PDF
        $pdf = Pdf::loadView('pdf.bills', compact('contract', 'owner'));

        return $pdf->stream('contrat-'.$contract->id.'.pdf');
    }

    public function preview($company, $period, $contractIds)
    {
        $period_bills = Carbon::createFromFormat('d-m-Y', substr($period, 0, 10))->format('m-Y');
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

        $pdf = Pdf::loadView('pdf.preview-bills', compact('contract', 'contracts', 'owner'))->setOption('enable-local-file-access', true);

        Storage::put($path, $pdf->output());

        return response()->file(storage_path("app/{$path}"));
    }
}
