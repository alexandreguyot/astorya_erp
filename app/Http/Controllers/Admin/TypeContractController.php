<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeContract;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TypeContractController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('type_contract_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.contract-type.index');
    }

    public function create()
    {
        abort_if(Gate::denies('type_contract_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.contract-type.create');
    }

    public function edit(TypeContract $typeContract)
    {
        abort_if(Gate::denies('type_contract_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.contract-type.edit', compact('typeContract'));
    }

    public function show(TypeContract $typeContract)
    {
        abort_if(Gate::denies('type_contract_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.contract-type.show', compact('typeContract'));
    }
}
