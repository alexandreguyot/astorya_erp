<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BankAccountController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('bank_account_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.bank-account.index');
    }

    public function create()
    {
        abort_if(Gate::denies('bank_account_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.bank-account.create');
    }

    public function edit(BankAccount $bankAccount)
    {
        abort_if(Gate::denies('bank_account_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.bank-account.edit', compact('bankAccount'));
    }

    public function show(BankAccount $bankAccount)
    {
        abort_if(Gate::denies('bank_account_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.bank-account.show', compact('bankAccount'));
    }
}
