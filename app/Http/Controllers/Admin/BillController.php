<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Owner;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class BillController extends Controller
{
    public function index()
    {
        return view('admin.bill.index');
    }

    public function pdf($billId) {
        $bill = Bill::findOrFail($billId);
        $owner = Owner::first();

        return view('admin.bill.pdf', compact('bill', 'owner'));
    }

}
