<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OwnerController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('owner_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.owner.index');
    }

    public function create()
    {
        abort_if(Gate::denies('owner_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.owner.create');
    }

    public function edit(Owner $owner)
    {
        abort_if(Gate::denies('owner_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.owner.edit', compact('owner'));
    }

    public function show(Owner $owner)
    {
        abort_if(Gate::denies('owner_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.owner.show', compact('owner'));
    }
}
