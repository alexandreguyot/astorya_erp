<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypePeriod;
use Illuminate\Http\Response;

class TypePeriodController extends Controller
{
    public function index()
    {
        return view('admin.type-period.index');
    }

    public function create()
    {
        return view('admin.type-period.create');
    }

    public function edit(TypePeriod $typePeriod)
    {
        return view('admin.type-period.edit', compact('typePeriod'));
    }

    public function show(TypePeriod $typePeriod)
    {
        return view('admin.type-period.show', compact('typePeriod'));
    }
}
