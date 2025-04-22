<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeProduct;

class TypeProductController extends Controller
{
    public function index()
    {
        return view('admin.type-product.index');
    }

    public function create()
    {
        return view('admin.type-product.create');
    }

    public function edit(TypeProduct $typeProduct)
    {
        return view('admin.type-product.edit', compact('typeProduct'));
    }

    public function show(TypeProduct $typeProduct)
    {
        return view('admin.type-product.show', compact('typeProduct'));
    }
}
