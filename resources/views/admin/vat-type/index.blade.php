@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.vatType.title') }}
                </h6>

                @can('vat_type_create')
                    <a class="btn btn-indigo" href="{{ route('admin.vat-types.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.vatType.title_singular') }}
                    </a>
                @endcan
            </div>
        </div>
        @livewire('vat-type.index')

    </div>
</div>
@endsection
