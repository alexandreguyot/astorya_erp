@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.vatType.title_singular') }}:
                    {{ trans('cruds.vatType.fields.id') }}
                    {{ $vatType->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('vat-type.edit', [$vatType])
        </div>
    </div>
</div>
@endsection