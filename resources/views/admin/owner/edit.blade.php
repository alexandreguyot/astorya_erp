@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.owner.title_singular') }}:
                    {{ trans('cruds.owner.fields.id') }}
                    {{ $owner->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('owner.edit', [$owner])
        </div>
    </div>
</div>
@endsection