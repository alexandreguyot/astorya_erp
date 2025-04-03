@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.typeProduct.title_singular') }}:
                    {{ trans('cruds.typeProduct.fields.id') }}
                    {{ $typeProduct->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('type-product.edit', [$typeProduct])
        </div>
    </div>
</div>
@endsection
