<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeVat;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TypeVatController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('type_vat_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.vat-type.index');
    }

    public function create()
    {
        abort_if(Gate::denies('type_vat_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.vat-type.create');
    }

    public function edit(TypeVat $typeVat)
    {
        abort_if(Gate::denies('type_vat_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.vat-type.edit', compact('typeVat'));
    }

    public function show(TypeVat $typeVat)
    {
        abort_if(Gate::denies('type_vat_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.vat-type.show', compact('typeVat'));
    }
}
