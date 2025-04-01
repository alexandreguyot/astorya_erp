<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('bill.no_bill') ? 'invalid' : '' }}">
        <label class="form-label" for="no_bill">{{ trans('cruds.bill.fields.no_bill') }}</label>
        <input class="form-control" type="text" name="no_bill" id="no_bill" wire:model.defer="bill.no_bill">
        <div class="validation-message">
            {{ $errors->first('bill.no_bill') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bill.fields.no_bill_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('bill.amount') ? 'invalid' : '' }}">
        <label class="form-label" for="amount">{{ trans('cruds.bill.fields.amount') }}</label>
        <input class="form-control" type="number" name="amount" id="amount" wire:model.defer="bill.amount" step="0.01">
        <div class="validation-message">
            {{ $errors->first('bill.amount') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bill.fields.amount_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('bill.amount_vat_included') ? 'invalid' : '' }}">
        <label class="form-label" for="amount_vat_included">{{ trans('cruds.bill.fields.amount_vat_included') }}</label>
        <input class="form-control" type="number" name="amount_vat_included" id="amount_vat_included" wire:model.defer="bill.amount_vat_included" step="0.01">
        <div class="validation-message">
            {{ $errors->first('bill.amount_vat_included') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bill.fields.amount_vat_included_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('bill.one_bill_per_period') ? 'invalid' : '' }}">
        <input class="form-control" type="checkbox" name="one_bill_per_period" id="one_bill_per_period" wire:model.defer="bill.one_bill_per_period">
        <label class="form-label inline ml-1" for="one_bill_per_period">{{ trans('cruds.bill.fields.one_bill_per_period') }}</label>
        <div class="validation-message">
            {{ $errors->first('bill.one_bill_per_period') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bill.fields.one_bill_per_period_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('bill.started_at') ? 'invalid' : '' }}">
        <label class="form-label" for="started_at">{{ trans('cruds.bill.fields.started_at') }}</label>
        <x-date-picker class="form-control" wire:model="bill.started_at" id="started_at" name="started_at" />
        <div class="validation-message">
            {{ $errors->first('bill.started_at') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bill.fields.started_at_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('bill.billed_at') ? 'invalid' : '' }}">
        <label class="form-label" for="billed_at">{{ trans('cruds.bill.fields.billed_at') }}</label>
        <x-date-picker class="form-control" wire:model="bill.billed_at" id="billed_at" name="billed_at" picker="date" />
        <div class="validation-message">
            {{ $errors->first('bill.billed_at') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bill.fields.billed_at_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('bill.generated_at') ? 'invalid' : '' }}">
        <label class="form-label" for="generated_at">{{ trans('cruds.bill.fields.generated_at') }}</label>
        <x-date-picker class="form-control" wire:model="bill.generated_at" id="generated_at" name="generated_at" />
        <div class="validation-message">
            {{ $errors->first('bill.generated_at') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bill.fields.generated_at_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('bill.validated_at') ? 'invalid' : '' }}">
        <label class="form-label" for="validated_at">{{ trans('cruds.bill.fields.validated_at') }}</label>
        <x-date-picker class="form-control" wire:model="bill.validated_at" id="validated_at" name="validated_at" />
        <div class="validation-message">
            {{ $errors->first('bill.validated_at') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bill.fields.validated_at_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('bill.sent_at') ? 'invalid' : '' }}">
        <label class="form-label" for="sent_at">{{ trans('cruds.bill.fields.sent_at') }}</label>
        <x-date-picker class="form-control" wire:model="bill.sent_at" id="sent_at" name="sent_at" />
        <div class="validation-message">
            {{ $errors->first('bill.sent_at') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bill.fields.sent_at_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('bill.to_be_collected') ? 'invalid' : '' }}">
        <input class="form-control" type="checkbox" name="to_be_collected" id="to_be_collected" wire:model.defer="bill.to_be_collected">
        <label class="form-label inline ml-1" for="to_be_collected">{{ trans('cruds.bill.fields.to_be_collected') }}</label>
        <div class="validation-message">
            {{ $errors->first('bill.to_be_collected') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bill.fields.to_be_collected_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('bill.collected_at') ? 'invalid' : '' }}">
        <label class="form-label" for="collected_at">{{ trans('cruds.bill.fields.collected_at') }}</label>
        <x-date-picker class="form-control" wire:model="bill.collected_at" id="collected_at" name="collected_at" />
        <div class="validation-message">
            {{ $errors->first('bill.collected_at') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bill.fields.collected_at_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('bill.recorded_at') ? 'invalid' : '' }}">
        <label class="form-label" for="recorded_at">{{ trans('cruds.bill.fields.recorded_at') }}</label>
        <x-date-picker class="form-control" wire:model="bill.recorded_at" id="recorded_at" name="recorded_at" />
        <div class="validation-message">
            {{ $errors->first('bill.recorded_at') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bill.fields.recorded_at_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('mediaCollections.bill_file_path') ? 'invalid' : '' }}">
        <label class="form-label" for="file_path">{{ trans('cruds.bill.fields.file_path') }}</label>
        <x-dropzone id="file_path" name="file_path" action="{{ route('admin.bills.storeMedia') }}" collection-name="bill_file_path" max-file-size="10" />
        <div class="validation-message">
            {{ $errors->first('mediaCollections.bill_file_path') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bill.fields.file_path_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('bill.company_id') ? 'invalid' : '' }}">
        <label class="form-label required" for="company">{{ trans('cruds.bill.fields.company') }}</label>
        <x-select-list class="form-control" required id="company" name="company" :options="$this->listsForFields['company']" wire:model="bill.company_id" />
        <div class="validation-message">
            {{ $errors->first('bill.company_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bill.fields.company_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('bill.type_period_id') ? 'invalid' : '' }}">
        <label class="form-label" for="type_period">{{ trans('cruds.bill.fields.type_period') }}</label>
        <x-select-list class="form-control" id="type_period" name="type_period" :options="$this->listsForFields['type_period']" wire:model="bill.type_period_id" />
        <div class="validation-message">
            {{ $errors->first('bill.type_period_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.bill.fields.type_period_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.bills.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
