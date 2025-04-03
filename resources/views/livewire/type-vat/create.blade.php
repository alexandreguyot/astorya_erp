<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('typeVat.code') ? 'invalid' : '' }}">
        <label class="form-label required" for="code">{{ trans('cruds.typeVat.fields.code') }}</label>
        <input class="form-control" type="text" name="code" id="code" required wire:model.defer="typeVat.code">
        <div class="validation-message">
            {{ $errors->first('typeVat.code') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.typeVat.fields.code_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('typeVat.percent') ? 'invalid' : '' }}">
        <label class="form-label" for="percent">{{ trans('cruds.typeVat.fields.percent') }}</label>
        <input class="form-control" type="number" name="percent" id="percent" wire:model.defer="typeVat.percent" step="0.01">
        <div class="validation-message">
            {{ $errors->first('typeVat.percent') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.typeVat.fields.percent_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.type-vat.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
