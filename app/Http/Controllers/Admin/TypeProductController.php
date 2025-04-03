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
        abort_if(Gate::denies('type_product_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.type-product.index');
    }

    public function create()
    {
        abort_if(Gate::denies('type_product_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.type-product.create');
    }

    public function edit(TypeProduct $typeProduct)
    {
        abort_if(Gate::denies('type_product_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.type-product.edit', compact('typeProduct'));
    }

    public function show(TypeProduct $typeProduct)
    {
        abort_if(Gate::denies('type_product_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.type-product.show', compact('typeProduct'));
    }
}
