<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('company.name') ? 'invalid' : '' }}">
        <label class="form-label" for="name">{{ trans('cruds.company.fields.name') }}</label>
        <input class="form-control" type="text" name="name" id="name" wire:model.defer="company.name">
        <div class="validation-message">
            {{ $errors->first('company.name') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.company.fields.name_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('company.address') ? 'invalid' : '' }}">
        <label class="form-label" for="address">{{ trans('cruds.company.fields.address') }}</label>
        <input class="form-control" type="text" name="address" id="address" wire:model.defer="company.address">
        <div class="validation-message">
            {{ $errors->first('company.address') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.company.fields.address_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('company.address_compl') ? 'invalid' : '' }}">
        <label class="form-label" for="address_compl">{{ trans('cruds.company.fields.address_compl') }}</label>
        <input class="form-control" type="text" name="address_compl" id="address_compl" wire:model.defer="company.address_compl">
        <div class="validation-message">
            {{ $errors->first('company.address_compl') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.company.fields.address_compl_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('company.city_id') ? 'invalid' : '' }}">
        <label class="form-label required" for="city">{{ trans('cruds.company.fields.city') }}</label>
        <x-select-list class="form-control" required id="city" name="city" :options="$this->listsForFields['city']" wire:model="company.city_id" />
        <div class="validation-message">
            {{ $errors->first('company.city_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.company.fields.city_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('company.email') ? 'invalid' : '' }}">
        <label class="form-label" for="email">{{ trans('cruds.company.fields.email') }}</label>
        <input class="form-control" type="email" name="email" id="email" wire:model.defer="company.email">
        <div class="validation-message">
            {{ $errors->first('company.email') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.company.fields.email_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('company.accounting') ? 'invalid' : '' }}">
        <label class="form-label" for="accounting">{{ trans('cruds.company.fields.accounting') }}</label>
        <input class="form-control" type="text" name="accounting" id="accounting" wire:model.defer="company.accounting">
        <div class="validation-message">
            {{ $errors->first('company.accounting') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.company.fields.accounting_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('company.ciel_reference') ? 'invalid' : '' }}">
        <label class="form-label" for="ciel_reference">{{ trans('cruds.company.fields.ciel_reference') }}</label>
        <input class="form-control" type="text" name="ciel_reference" id="ciel_reference" wire:model.defer="company.ciel_reference">
        <div class="validation-message">
            {{ $errors->first('company.ciel_reference') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.company.fields.ciel_reference_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('company.send_bill_type') ? 'invalid' : '' }}">
        <input class="form-control" type="checkbox" name="send_bill_type" id="send_bill_type" wire:model.defer="company.send_bill_type">
        <label class="form-label inline ml-1" for="send_bill_type">{{ trans('cruds.company.fields.send_bill_type') }}</label>
        <div class="validation-message">
            {{ $errors->first('company.send_bill_type') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.company.fields.send_bill_type_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('company.one_bill_per_period') ? 'invalid' : '' }}">
        <input class="form-control" type="checkbox" name="one_bill_per_period" id="one_bill_per_period" wire:model.defer="company.one_bill_per_period">
        <label class="form-label inline ml-1" for="one_bill_per_period">{{ trans('cruds.company.fields.one_bill_per_period') }}</label>
        <div class="validation-message">
            {{ $errors->first('company.one_bill_per_period') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.company.fields.one_bill_per_period_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('company.bill_payment_methood') ? 'invalid' : '' }}">
        <label class="form-label" for="bill_payment_methood">{{ trans('cruds.company.fields.bill_payment_methood') }}</label>
        <input class="form-control" type="text" name="bill_payment_methood" id="bill_payment_methood" wire:model.defer="company.bill_payment_methood">
        <div class="validation-message">
            {{ $errors->first('company.bill_payment_methood') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.company.fields.bill_payment_methood_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('company.observations') ? 'invalid' : '' }}">
        <label class="form-label" for="observations">{{ trans('cruds.company.fields.observations') }}</label>
        <textarea class="form-control" name="observations" id="observations" wire:model.defer="company.observations" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('company.observations') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.company.fields.observations_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>