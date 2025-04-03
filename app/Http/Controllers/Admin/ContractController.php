<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Bill;
use App\Models\Owner;
use Barryvdh\DomPDF\Facade\Pdf;

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

}
