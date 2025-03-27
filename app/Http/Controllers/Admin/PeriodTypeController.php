<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodType;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PeriodTypeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('period_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.period-type.index');
    }

    public function create()
    {
        abort_if(Gate::denies('period_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.period-type.create');
    }

    public function edit(PeriodType $periodType)
    {
        abort_if(Gate::denies('period_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.period-type.edit', compact('periodType'));
    }

    public function show(PeriodType $periodType)
    {
        abort_if(Gate::denies('period_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.period-type.show', compact('periodType'));
    }
}
