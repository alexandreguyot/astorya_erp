<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypeContract;

class TypeContractController extends Controller
{
    public function index()
    {
        return view('admin.type-contract.index');
    }

    public function create()
    {
        return view('admin.type-contract.create');
    }

    public function edit(TypeContract $typeContract)
    {
        return view('admin.type-contract.edit', compact('typeContract'));
    }

    public function show(TypeContract $typeContract)
    {
        return view('admin.type-contract.show', compact('typeContract'));
    }
}
