@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.productType.title') }}
                </h6>

                @can('product_type_create')
                    <a class="btn btn-indigo" href="{{ route('admin.product-types.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.productType.title_singular') }}
                    </a>
                @endcan
            </div>
        </div>
        @livewire('product-type.index')

    </div>
</div>
@endsection
