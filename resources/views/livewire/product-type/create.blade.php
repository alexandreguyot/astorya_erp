<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('productType.code') ? 'invalid' : '' }}">
        <label class="form-label required" for="code">{{ trans('cruds.productType.fields.code') }}</label>
        <input class="form-control" type="text" name="code" id="code" required wire:model.defer="productType.code">
        <div class="validation-message">
            {{ $errors->first('productType.code') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.productType.fields.code_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('productType.short_description') ? 'invalid' : '' }}">
        <label class="form-label" for="short_description">{{ trans('cruds.productType.fields.short_description') }}</label>
        <textarea class="form-control" name="short_description" id="short_description" wire:model.defer="productType.short_description" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('productType.short_description') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.productType.fields.short_description_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('productType.description_longue') ? 'invalid' : '' }}">
        <label class="form-label" for="description_longue">{{ trans('cruds.productType.fields.description_longue') }}</label>
        <textarea class="form-control" name="description_longue" id="description_longue" wire:model.defer="productType.description_longue" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('productType.description_longue') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.productType.fields.description_longue_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('productType.accounting') ? 'invalid' : '' }}">
        <label class="form-label" for="accounting">{{ trans('cruds.productType.fields.accounting') }}</label>
        <input class="form-control" type="text" name="accounting" id="accounting" wire:model.defer="productType.accounting">
        <div class="validation-message">
            {{ $errors->first('productType.accounting') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.productType.fields.accounting_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.product-types.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>