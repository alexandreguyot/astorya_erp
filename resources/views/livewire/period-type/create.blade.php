<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('periodType.title') ? 'invalid' : '' }}">
        <label class="form-label required" for="title">{{ trans('cruds.periodType.fields.title') }}</label>
        <input class="form-control" type="text" name="title" id="title" required wire:model.defer="periodType.title">
        <div class="validation-message">
            {{ $errors->first('periodType.title') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.periodType.fields.title_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('periodType.nb_month') ? 'invalid' : '' }}">
        <label class="form-label required" for="nb_month">{{ trans('cruds.periodType.fields.nb_month') }}</label>
        <input class="form-control" type="number" name="nb_month" id="nb_month" required wire:model.defer="periodType.nb_month" step="1">
        <div class="validation-message">
            {{ $errors->first('periodType.nb_month') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.periodType.fields.nb_month_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.period-types.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>