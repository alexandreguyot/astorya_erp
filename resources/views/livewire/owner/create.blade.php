<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('owner.name') ? 'invalid' : '' }}">
        <label class="form-label required" for="name">{{ trans('cruds.owner.fields.name') }}</label>
        <input class="form-control" type="text" name="name" id="name" required wire:model.defer="owner.name">
        <div class="validation-message">
            {{ $errors->first('owner.name') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.name_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('owner.address') ? 'invalid' : '' }}">
        <label class="form-label required" for="address">{{ trans('cruds.owner.fields.address') }}</label>
        <input class="form-control" type="text" name="address" id="address" required wire:model.defer="owner.address">
        <div class="validation-message">
            {{ $errors->first('owner.address') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.address_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('owner.zip_code') ? 'invalid' : '' }}">
        <label class="form-label required" for="zip_code">{{ trans('cruds.owner.fields.zip_code') }}</label>
        <input class="form-control" type="text" name="zip_code" id="zip_code" required wire:model.defer="owner.zip_code">
        <div class="validation-message">
            {{ $errors->first('owner.zip_code') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.zip_code_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('owner.city') ? 'invalid' : '' }}">
        <label class="form-label required" for="city">{{ trans('cruds.owner.fields.city') }}</label>
        <input class="form-control" type="text" name="city" id="city" required wire:model.defer="owner.city">
        <div class="validation-message">
            {{ $errors->first('owner.city') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.city_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('owner.email') ? 'invalid' : '' }}">
        <label class="form-label required" for="email">{{ trans('cruds.owner.fields.email') }}</label>
        <input class="form-control" type="text" name="email" id="email" required wire:model.defer="owner.email">
        <div class="validation-message">
            {{ $errors->first('owner.email') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.email_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('owner.phone') ? 'invalid' : '' }}">
        <label class="form-label required" for="phone">{{ trans('cruds.owner.fields.phone') }}</label>
        <input class="form-control" type="text" name="phone" id="phone" required wire:model.defer="owner.phone">
        <div class="validation-message">
            {{ $errors->first('owner.phone') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.phone_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('owner.web_site_address') ? 'invalid' : '' }}">
        <label class="form-label required" for="web_site_address">{{ trans('cruds.owner.fields.web_site_address') }}</label>
        <input class="form-control" type="text" name="web_site_address" id="web_site_address" required wire:model.defer="owner.web_site_address">
        <div class="validation-message">
            {{ $errors->first('owner.web_site_address') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.web_site_address_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('owner.siret') ? 'invalid' : '' }}">
        <label class="form-label required" for="siret">{{ trans('cruds.owner.fields.siret') }}</label>
        <input class="form-control" type="text" name="siret" id="siret" required wire:model.defer="owner.siret">
        <div class="validation-message">
            {{ $errors->first('owner.siret') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.siret_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('owner.capital') ? 'invalid' : '' }}">
        <label class="form-label required" for="capital">{{ trans('cruds.owner.fields.capital') }}</label>
        <input class="form-control" type="text" name="capital" id="capital" required wire:model.defer="owner.capital">
        <div class="validation-message">
            {{ $errors->first('owner.capital') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.capital_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('owner.bic') ? 'invalid' : '' }}">
        <label class="form-label required" for="bic">{{ trans('cruds.owner.fields.bic') }}</label>
        <input class="form-control" type="text" name="bic" id="bic" required wire:model.defer="owner.bic">
        <div class="validation-message">
            {{ $errors->first('owner.bic') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.bic_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('owner.iban') ? 'invalid' : '' }}">
        <label class="form-label required" for="iban">{{ trans('cruds.owner.fields.iban') }}</label>
        <input class="form-control" type="text" name="iban" id="iban" required wire:model.defer="owner.iban">
        <div class="validation-message">
            {{ $errors->first('owner.iban') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.iban_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('owner.hotline_name') ? 'invalid' : '' }}">
        <label class="form-label required" for="hotline_name">{{ trans('cruds.owner.fields.hotline_name') }}</label>
        <input class="form-control" type="text" name="hotline_name" id="hotline_name" required wire:model.defer="owner.hotline_name">
        <div class="validation-message">
            {{ $errors->first('owner.hotline_name') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.hotline_name_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('owner.hotline_phone') ? 'invalid' : '' }}">
        <label class="form-label required" for="hotline_phone">{{ trans('cruds.owner.fields.hotline_phone') }}</label>
        <input class="form-control" type="text" name="hotline_phone" id="hotline_phone" required wire:model.defer="owner.hotline_phone">
        <div class="validation-message">
            {{ $errors->first('owner.hotline_phone') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.hotline_phone_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('owner.hotline_email') ? 'invalid' : '' }}">
        <label class="form-label required" for="hotline_email">{{ trans('cruds.owner.fields.hotline_email') }}</label>
        <input class="form-control" type="text" name="hotline_email" id="hotline_email" required wire:model.defer="owner.hotline_email">
        <div class="validation-message">
            {{ $errors->first('owner.hotline_email') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.hotline_email_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('owner.accounting_manager') ? 'invalid' : '' }}">
        <label class="form-label required" for="accounting_manager">{{ trans('cruds.owner.fields.accounting_manager') }}</label>
        <input class="form-control" type="text" name="accounting_manager" id="accounting_manager" required wire:model.defer="owner.accounting_manager">
        <div class="validation-message">
            {{ $errors->first('owner.accounting_manager') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.accounting_manager_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('owner.accounting_phone') ? 'invalid' : '' }}">
        <label class="form-label required" for="accounting_phone">{{ trans('cruds.owner.fields.accounting_phone') }}</label>
        <input class="form-control" type="text" name="accounting_phone" id="accounting_phone" required wire:model.defer="owner.accounting_phone">
        <div class="validation-message">
            {{ $errors->first('owner.accounting_phone') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.accounting_phone_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('owner.accounting_email') ? 'invalid' : '' }}">
        <label class="form-label required" for="accounting_email">{{ trans('cruds.owner.fields.accounting_email') }}</label>
        <input class="form-control" type="text" name="accounting_email" id="accounting_email" required wire:model.defer="owner.accounting_email">
        <div class="validation-message">
            {{ $errors->first('owner.accounting_email') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.owner.fields.accounting_email_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.owners.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>