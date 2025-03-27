@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.owner.title') }}
                </h6>

                @can('owner_create')
                    <a class="btn btn-indigo" href="{{ route('admin.owners.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.owner.title_singular') }}
                    </a>
                @endcan
            </div>
        </div>
        @livewire('owner.index')

    </div>
</div>
@endsection
