@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.city.title_singular') }}:
                    {{ trans('cruds.city.fields.id') }}
                    {{ $city->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('city.edit', [$city])
        </div>
    </div>
</div>
@endsection