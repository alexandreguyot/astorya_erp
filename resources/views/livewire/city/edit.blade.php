<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('city.zipcode') ? 'invalid' : '' }}">
        <label class="form-label required" for="zipcode">{{ trans('cruds.city.fields.zipcode') }}</label>
        <input class="form-control" type="text" name="zipcode" id="zipcode" required wire:model.defer="city.zipcode">
        <div class="validation-message">
            {{ $errors->first('city.zipcode') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.city.fields.zipcode_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('city.name') ? 'invalid' : '' }}">
        <label class="form-label required" for="name">{{ trans('cruds.city.fields.name') }}</label>
        <input class="form-control" type="text" name="name" id="name" required wire:model.defer="city.name">
        <div class="validation-message">
            {{ $errors->first('city.name') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.city.fields.name_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>