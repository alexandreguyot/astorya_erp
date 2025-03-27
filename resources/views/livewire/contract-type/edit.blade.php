<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('contractType.title') ? 'invalid' : '' }}">
        <label class="form-label required" for="title">{{ trans('cruds.contractType.fields.title') }}</label>
        <input class="form-control" type="text" name="title" id="title" required wire:model.defer="contractType.title">
        <div class="validation-message">
            {{ $errors->first('contractType.title') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contractType.fields.title_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.contract-types.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>