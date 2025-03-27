<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContactController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('contact_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.contact.index');
    }

    public function create()
    {
        abort_if(Gate::denies('contact_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.contact.create');
    }

    public function edit(Contact $contact)
    {
        abort_if(Gate::denies('contact_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.contact.edit', compact('contact'));
    }

    public function show(Contact $contact)
    {
        abort_if(Gate::denies('contact_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.contact.show', compact('contact'));
    }
}
