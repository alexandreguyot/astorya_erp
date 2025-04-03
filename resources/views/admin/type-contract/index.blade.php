@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.typeContract.title') }}
                </h6>

                @can('type_contract_create')
                    <a class="btn btn-indigo" href="{{ route('admin.type-contract.create') }}">
                        Créer un type de contrat
                    </a>
                @endcan
            </div>
        </div>
        @livewire('type-contract.index')

    </div>
</div>
@endsection
