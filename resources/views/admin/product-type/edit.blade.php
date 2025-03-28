@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.productType.title_singular') }}:
                    {{ trans('cruds.productType.fields.id') }}
                    {{ $productType->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('product-type.edit', [$productType])
        </div>
    </div>
</div>
@endsection