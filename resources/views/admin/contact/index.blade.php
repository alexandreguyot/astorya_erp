@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.contact.title') }}
                </h6>

                @can('contact_create')
                    <a class="btn btn-indigo" href="{{ route('admin.contacts.create') }}">
                        Créer un contact
                    </a>
                @endcan
            </div>
        </div>
        @livewire('contact.index')

    </div>
</div>
@endsection
