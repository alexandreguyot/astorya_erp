<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('typeProduct.code') ? 'invalid' : '' }}">
        <label class="form-label required" for="code">{{ trans('cruds.typeProduct.fields.code') }}</label>
        <input class="form-control" type="text" name="code" id="code" required wire:model.defer="typeProduct.code">
        <div class="validation-message">
            {{ $errors->first('typeProduct.code') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.typeProduct.fields.code_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('typeProduct.short_description') ? 'invalid' : '' }}">
        <label class="form-label" for="short_description">{{ trans('cruds.typeProduct.fields.short_description') }}</label>
        <textarea class="form-control" name="short_description" id="short_description" wire:model.defer="typeProduct.short_description" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('typeProduct.short_description') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.typeProduct.fields.short_description_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('typeProduct.description_longue') ? 'invalid' : '' }}">
        <label class="form-label" for="description_longue">{{ trans('cruds.typeProduct.fields.description_longue') }}</label>
        <textarea class="form-control" name="description_longue" id="description_longue" wire:model.defer="typeProduct.description_longue" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('typeProduct.description_longue') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.typeProduct.fields.description_longue_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('typeProduct.accounting') ? 'invalid' : '' }}">
        <label class="form-label" for="accounting">{{ trans('cruds.typeProduct.fields.accounting') }}</label>
        <input class="form-control" type="text" name="accounting" id="accounting" wire:model.defer="typeProduct.accounting">
        <div class="validation-message">
            {{ $errors->first('typeProduct.accounting') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.typeProduct.fields.accounting_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.type-product.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
