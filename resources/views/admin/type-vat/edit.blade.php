@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.typeVat.title_singular') }}:
                    {{ trans('cruds.typeVat.fields.id') }}
                    {{ $typeVat->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('type-vat.edit', [$typeVat])
        </div>
    </div>
</div>
@endsection
