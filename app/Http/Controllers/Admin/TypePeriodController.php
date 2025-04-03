<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TypePeriod;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TypePeriodController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('type_period_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.type-period.index');
    }

    public function create()
    {
        abort_if(Gate::denies('type_period_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.type-period.create');
    }

    public function edit(TypePeriod $typePeriod)
    {
        abort_if(Gate::denies('type_period_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.type-period.edit', compact('typePeriod'));
    }

    public function show(TypePeriod $typePeriod)
    {
        abort_if(Gate::denies('type_period_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.type-period.show', compact('typePeriod'));
    }
}
