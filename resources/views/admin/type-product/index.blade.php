@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.typeProduct.title') }}
                </h6>

                @can('type_product_create')
                    <a class="btn btn-indigo" href="{{ route('admin.type-product.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.typeProduct.title_singular') }}
                    </a>
                @endcan
            </div>
        </div>
        @livewire('type-product.index')

    </div>
</div>
@endsection
