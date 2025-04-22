<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;

class CompanyController extends Controller
{
    public function index()
    {
        return view('admin.company.index');
    }

    public function create()
    {
        return view('admin.company.create');
    }

    public function edit(Company $company)
    {
        return view('admin.company.edit', compact('company'));
    }

    public function show(Company $company)
    {
        $company->load('city');

        return view('admin.company.show', compact('company'));
    }
}
