@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.contact.title_singular') }}:
                    {{ trans('cruds.contact.fields.id') }}
                    {{ $contact->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('contact.edit', [$contact])
        </div>
    </div>
</div>
@endsection