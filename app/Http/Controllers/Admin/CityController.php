<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;

class CityController extends Controller
{
    public function index()
    {
        return view('admin.city.index');
    }

    public function create()
    {
        return view('admin.city.create');
    }

    public function edit(City $city)
    {
        return view('admin.city.edit', compact('city'));
    }

    public function show(City $city)
    {
        return view('admin.city.show', compact('city'));
    }
}
