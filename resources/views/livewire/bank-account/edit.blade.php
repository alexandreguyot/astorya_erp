<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('bankAccount.no_rum') ? 'invalid' : '' }}">
        <label class="form-label" for="no_rum">{{ trans('cruds.bankAccount.fields.no_rum') }}</label>
        <input class="form-control" type="text" name="no_rum" id="no_rum" wire:model.defer="bankAccount.no_rum">
        <div class="validation-message">
            {{ $errors->first('bankAccount.no_rum') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bankAccount.fields.no_rum_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('bankAccount.effective_start_date') ? 'invalid' : '' }}">
        <label class="form-label" for="effective_start_date">{{ trans('cruds.bankAccount.fields.effective_start_date') }}</label>
        <x-date-picker class="form-control" wire:model="bankAccount.effective_start_date" id="effective_start_date" name="effective_start_date" picker="date" />
        <div class="validation-message">
            {{ $errors->first('bankAccount.effective_start_date') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bankAccount.fields.effective_start_date_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('bankAccount.bic') ? 'invalid' : '' }}">
        <label class="form-label" for="bic">{{ trans('cruds.bankAccount.fields.bic') }}</label>
        <input class="form-control" type="text" name="bic" id="bic" wire:model.defer="bankAccount.bic">
        <div class="validation-message">
            {{ $errors->first('bankAccount.bic') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bankAccount.fields.bic_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('bankAccount.iban') ? 'invalid' : '' }}">
        <label class="form-label" for="iban">{{ trans('cruds.bankAccount.fields.iban') }}</label>
        <input class="form-control" type="text" name="iban" id="iban" wire:model.defer="bankAccount.iban">
        <div class="validation-message">
            {{ $errors->first('bankAccount.iban') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bankAccount.fields.iban_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.bank-accounts.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
