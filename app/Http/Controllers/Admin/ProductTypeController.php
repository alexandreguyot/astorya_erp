<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductType;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductTypeController extends Controller
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

    public function edit(ProductType $productType)
    {
        abort_if(Gate::denies('product_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.product-type.edit', compact('productType'));
    }

    public function show(ProductType $productType)
    {
        abort_if(Gate::denies('product_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.product-type.show', compact('productType'));
    }
}
