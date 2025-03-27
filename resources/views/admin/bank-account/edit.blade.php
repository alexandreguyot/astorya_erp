@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.bankAccount.title_singular') }}:
                    {{ trans('cruds.bankAccount.fields.id') }}
                    {{ $bankAccount->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('bank-account.edit', [$bankAccount])
        </div>
    </div>
</div>
@endsection