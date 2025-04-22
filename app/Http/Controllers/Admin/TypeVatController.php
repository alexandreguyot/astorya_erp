<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeVat;
use Illuminate\Http\Response;

class TypeVatController extends Controller
{
    public function index()
    {
        return view('admin.type-vat.index');
    }

    public function create()
    {
        return view('admin.type-vat.create');
    }

    public function edit(TypeVat $typeVat)
    {
        return view('admin.type-vat.edit', compact('typeVat'));
    }

    public function show(TypeVat $typeVat)
    {
        return view('admin.type-vat.show', compact('typeVat'));
    }
}
