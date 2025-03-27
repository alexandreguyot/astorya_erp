<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContractController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('contract_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.contract.index');
    }

    public function create()
    {
        abort_if(Gate::denies('contract_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.contract.create');
    }

    public function edit(Contract $contract)
    {
        abort_if(Gate::denies('contract_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.contract.edit', compact('contract'));
    }

    public function show(Contract $contract)
    {
        abort_if(Gate::denies('contract_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $contract->load('company');

        return view('admin.contract.show', compact('contract'));
    }
}
