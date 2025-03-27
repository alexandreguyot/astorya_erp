<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('contract.company_id') ? 'invalid' : '' }}">
        <label class="form-label" for="company">{{ trans('cruds.contract.fields.company') }}</label>
        <x-select-list class="form-control" id="company" name="company" :options="$this->listsForFields['company']" wire:model="contract.company_id" />
        <div class="validation-message">
            {{ $errors->first('contract.company_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contract.fields.company_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('contract.setup_at') ? 'invalid' : '' }}">
        <label class="form-label" for="setup_at">{{ trans('cruds.contract.fields.setup_at') }}</label>
        <x-date-picker class="form-control" wire:model="contract.setup_at" id="setup_at" name="setup_at" />
        <div class="validation-message">
            {{ $errors->first('contract.setup_at') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contract.fields.setup_at_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('contract.established_at') ? 'invalid' : '' }}">
        <label class="form-label" for="established_at">{{ trans('cruds.contract.fields.established_at') }}</label>
        <x-date-picker class="form-control" wire:model="contract.established_at" id="established_at" name="established_at" />
        <div class="validation-message">
            {{ $errors->first('contract.established_at') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contract.fields.established_at_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('contract.started_at') ? 'invalid' : '' }}">
        <label class="form-label" for="started_at">{{ trans('cruds.contract.fields.started_at') }}</label>
        <x-date-picker class="form-control" wire:model="contract.started_at" id="started_at" name="started_at" />
        <div class="validation-message">
            {{ $errors->first('contract.started_at') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contract.fields.started_at_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('contract.terminated_at') ? 'invalid' : '' }}">
        <label class="form-label" for="terminated_at">{{ trans('cruds.contract.fields.terminated_at') }}</label>
        <x-date-picker class="form-control" wire:model="contract.terminated_at" id="terminated_at" name="terminated_at" />
        <div class="validation-message">
            {{ $errors->first('contract.terminated_at') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contract.fields.terminated_at_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('contract.billed_at') ? 'invalid' : '' }}">
        <label class="form-label" for="billed_at">{{ trans('cruds.contract.fields.billed_at') }}</label>
        <x-date-picker class="form-control" wire:model="contract.billed_at" id="billed_at" name="billed_at" />
        <div class="validation-message">
            {{ $errors->first('contract.billed_at') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contract.fields.billed_at_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('contract.validated_at') ? 'invalid' : '' }}">
        <label class="form-label" for="validated_at">{{ trans('cruds.contract.fields.validated_at') }}</label>
        <x-date-picker class="form-control" wire:model="contract.validated_at" id="validated_at" name="validated_at" />
        <div class="validation-message">
            {{ $errors->first('contract.validated_at') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contract.fields.validated_at_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>