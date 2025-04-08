@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.typePeriod.title') }}
                </h6>

                {{-- @can('type_period_create')
                    <a class="btn btn-indigo" href="{{ route('admin.type-period.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.typePeriod.title_singular') }}
                    </a>
                @endcan --}}
            </div>
        </div>
        @livewire('type-period.index')

    </div>
</div>
@endsection
