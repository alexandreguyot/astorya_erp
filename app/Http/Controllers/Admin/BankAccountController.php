<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
class BankAccountController extends Controller
{
    public function index()
    {
        return view('admin.bank-account.index');
    }

    public function create()
    {
        return view('admin.bank-account.create');
    }

    public function edit(BankAccount $bankAccount)
    {
        return view('admin.bank-account.edit', compact('bankAccount'));
    }

    public function show(BankAccount $bankAccount)
    {
        return view('admin.bank-account.show', compact('bankAccount'));
    }
}
