<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('typePeriod.title') ? 'invalid' : '' }}">
        <label class="form-label required" for="title">{{ trans('cruds.typePeriod.fields.title') }}</label>
        <input class="form-control" type="text" name="title" id="title" required wire:model.defer="typePeriod.title">
        <div class="validation-message">
            {{ $errors->first('typePeriod.title') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.typePeriod.fields.title_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('typePeriod.nb_month') ? 'invalid' : '' }}">
        <label class="form-label required" for="nb_month">{{ trans('cruds.typePeriod.fields.nb_month') }}</label>
        <input class="form-control" type="number" name="nb_month" id="nb_month" required wire:model.defer="typePeriod.nb_month" step="1">
        <div class="validation-message">
            {{ $errors->first('typePeriod.nb_month') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.typePeriod.fields.nb_month_helper') }}
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
