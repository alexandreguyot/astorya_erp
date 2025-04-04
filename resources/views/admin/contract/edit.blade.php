@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.contract.title_singular') }}:
                    {{ trans('cruds.contract.fields.id') }}
                    {{ $contract->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('contract.edit', [$contract])
        </div>
    </div>
</div>
@endsection