<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('vatType.code') ? 'invalid' : '' }}">
        <label class="form-label required" for="code">{{ trans('cruds.vatType.fields.code') }}</label>
        <input class="form-control" type="text" name="code" id="code" required wire:model.defer="vatType.code">
        <div class="validation-message">
            {{ $errors->first('vatType.code') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.vatType.fields.code_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('vatType.percent') ? 'invalid' : '' }}">
        <label class="form-label" for="percent">{{ trans('cruds.vatType.fields.percent') }}</label>
        <input class="form-control" type="number" name="percent" id="percent" wire:model.defer="vatType.percent" step="0.01">
        <div class="validation-message">
            {{ $errors->first('vatType.percent') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.vatType.fields.percent_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.vat-types.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>