<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContractController extends Controller
{
    public function index() {
        return view('admin.contract.index');
    }

    public function create() {
        return view('admin.contract.create');
    }

    public function edit(Contract $contract) {
        return view('admin.contract.edit', compact('contract'));
    }
}
