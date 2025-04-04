@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.typeContract.title_singular') }}:
                    {{ trans('cruds.typeContract.fields.id') }}
                    {{ $typeContract->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('type-contract.edit', [$typeContract])
        </div>
    </div>
</div>
@endsection
