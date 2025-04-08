@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.typeVat.title') }}
                </h6>

                {{-- @can('type_vat_create')
                    <a class="btn btn-indigo" href="{{ route('admin.type-vat.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.typeVat.title_singular') }}
                    </a>
                @endcan --}}
            </div>
        </div>
        @livewire('type-vat.index')

    </div>
</div>
@endsection
