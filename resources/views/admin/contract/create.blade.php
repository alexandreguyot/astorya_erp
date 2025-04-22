@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    Ajouter un contrat pour le client {{ $company->name }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('contract.create', [$company])
        </div>
    </div>
</div>
@endsection
