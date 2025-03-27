<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BillController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('bill_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.bill.index');
    }

    public function create()
    {
        abort_if(Gate::denies('bill_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.bill.create');
    }

    public function edit(Bill $bill)
    {
        abort_if(Gate::denies('bill_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.bill.edit', compact('bill'));
    }

    public function show(Bill $bill)
    {
        abort_if(Gate::denies('bill_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bill->load('company', 'typePeriod');

        return view('admin.bill.show', compact('bill'));
    }

    public function storeMedia(Request $request)
    {
        abort_if(Gate::none(['bill_create', 'bill_edit']), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->has('size')) {
            $this->validate($request, [
                'file' => 'max:' . $request->input('size') * 1024,
            ]);
        }

        $model                     = new Bill();
        $model->id                 = $request->input('model_id', 0);
        $model->exists             = true;
        $media                     = $model->addMediaFromRequest('file')->toMediaCollection($request->input('collection_name'));
        $media->wasRecentlyCreated = true;

        return response()->json(compact('media'), Response::HTTP_CREATED);
    }
}
