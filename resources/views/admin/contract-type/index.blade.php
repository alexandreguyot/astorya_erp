@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.contractType.title') }}
                </h6>

                @can('contract_type_create')
                    <a class="btn btn-indigo" href="{{ route('admin.contract-types.create') }}">
                        Créer un type de contrat
                    </a>
                @endcan
            </div>
        </div>
        @livewire('contract-type.index')

    </div>
</div>
@endsection
