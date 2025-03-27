<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VatType;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VatTypeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('vat_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.vat-type.index');
    }

    public function create()
    {
        abort_if(Gate::denies('vat_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.vat-type.create');
    }

    public function edit(VatType $vatType)
    {
        abort_if(Gate::denies('vat_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.vat-type.edit', compact('vatType'));
    }

    public function show(VatType $vatType)
    {
        abort_if(Gate::denies('vat_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.vat-type.show', compact('vatType'));
    }
}
