<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        return view('admin.contact.index');
    }

    public function create()
    {
        return view('admin.contact.create');
    }

    public function edit(Contact $contact)
    {
        return view('admin.contact.edit', compact('contact'));
    }

    public function show(Contact $contact)
    {
        return view('admin.contact.show', compact('contact'));
    }
}
