@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.city.title') }}
                </h6>

                @can('city_create')
                    <a class="btn btn-indigo" href="{{ route('admin.cities.create') }}">
                        Créer une ville
                    </a>
                @endcan
            </div>
        </div>
        @livewire('city.index')

    </div>
</div>
@endsection
