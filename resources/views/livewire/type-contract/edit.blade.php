<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('typeContract.title') ? 'invalid' : '' }}">
        <label class="form-label required" for="title">{{ trans('cruds.typeContract.fields.title') }}</label>
        <input class="form-control" type="text" name="title" id="title" required wire:model.defer="typeContract.title">
        <div class="validation-message">
            {{ $errors->first('typeContract.title') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.typeContract.fields.title_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.type-contract.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
