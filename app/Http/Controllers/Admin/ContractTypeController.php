<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractType;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContractTypeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('contract_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.contract-type.index');
    }

    public function create()
    {
        abort_if(Gate::denies('contract_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.contract-type.create');
    }

    public function edit(ContractType $contractType)
    {
        abort_if(Gate::denies('contract_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.contract-type.edit', compact('contractType'));
    }

    public function show(ContractType $contractType)
    {
        abort_if(Gate::denies('contract_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.contract-type.show', compact('contractType'));
    }
}
