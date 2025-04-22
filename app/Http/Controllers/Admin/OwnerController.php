<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;

class OwnerController extends Controller
{
    public function index()
    {
        return view('admin.owner.index');
    }

    public function create()
    {
        return view('admin.owner.create');
    }

    public function edit(Owner $owner)
    {
        return view('admin.owner.edit', compact('owner'));
    }

    public function show(Owner $owner)
    {
        return view('admin.owner.show', compact('owner'));
    }
}
