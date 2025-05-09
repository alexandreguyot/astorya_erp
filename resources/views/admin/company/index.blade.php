@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.company.title') }}
                </h6>

                @can('company_create')
                    <a class="btn btn-indigo" href="{{ route('admin.companies.create') }}">
                        Créer un client
                    </a>
                @endcan
            </div>
        </div>
        @livewire('company.index')

    </div>
</div>
@endsection
