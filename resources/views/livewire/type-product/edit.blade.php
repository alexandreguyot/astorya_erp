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
    <div class="form-group {{ $errors->has('typeProduct.designation_short') ? 'invalid' : '' }}">
        <label class="form-label" for="designation_short">{{ trans('cruds.typeProduct.fields.designation_short') }}</label>
        <textarea class="form-control" name="designation_short" id="designation_short" wire:model.defer="typeProduct.designation_short" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('typeProduct.designation_short') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.typeProduct.fields.designation_short_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('typeProduct.designation_long') ? 'invalid' : '' }}">
        <label class="form-label" for="designation_long">{{ trans('cruds.typeProduct.fields.designation_long') }}</label>
        <textarea class="form-control" name="designation_long" id="designation_long" wire:model.defer="typeProduct.designation_long" rows="4"></textarea>
        <div class="validation-message">
            {{ $errors->first('typeProduct.designation_long') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.typeProduct.fields.designation_long_helper') }}
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
