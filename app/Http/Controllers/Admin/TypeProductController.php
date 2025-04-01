<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeProduct;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TypeProductController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('product_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.product-type.index');
    }

    public function create()
    {
        abort_if(Gate::denies('product_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.product-type.create');
    }

    public function edit(TypeProduct $typeProduct)
    {
        abort_if(Gate::denies('product_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.product-type.edit', compact('typeProduct'));
    }

    public function show(TypeProduct $typeProduct)
    {
        abort_if(Gate::denies('product_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.product-type.show', compact('typeProduct'));
    }
}
