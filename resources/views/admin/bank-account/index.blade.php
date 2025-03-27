@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.bankAccount.title') }}
                </h6>

                @can('bank_account_create')
                    <a class="btn btn-indigo" href="{{ route('admin.bank-accounts.create') }}">
                        Créer un compte bancaire
                    </a>
                @endcan
            </div>
        </div>
        @livewire('bank-account.index')

    </div>
</div>
@endsection
