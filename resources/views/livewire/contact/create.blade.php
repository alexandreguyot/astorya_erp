<form wire:submit.prevent="submit" class="pt-3">

    <div class="form-group {{ $errors->has('contact.lastname') ? 'invalid' : '' }}">
        <label class="form-label" for="lastname">{{ trans('cruds.contact.fields.lastname') }}</label>
        <input class="form-control" type="text" name="lastname" id="lastname" wire:model.defer="contact.lastname">
        <div class="validation-message">
            {{ $errors->first('contact.lastname') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contact.fields.lastname_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('contact.firstname') ? 'invalid' : '' }}">
        <label class="form-label" for="firstname">{{ trans('cruds.contact.fields.firstname') }}</label>
        <input class="form-control" type="text" name="firstname" id="firstname" wire:model.defer="contact.firstname">
        <div class="validation-message">
            {{ $errors->first('contact.firstname') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contact.fields.firstname_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('contact.title') ? 'invalid' : '' }}">
        <label class="form-label" for="title">{{ trans('cruds.contact.fields.title') }}</label>
        <input class="form-control" type="text" name="title" id="title" wire:model.defer="contact.title">
        <div class="validation-message">
            {{ $errors->first('contact.title') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contact.fields.title_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('contact.email') ? 'invalid' : '' }}">
        <label class="form-label" for="email">{{ trans('cruds.contact.fields.email') }}</label>
        <input class="form-control" type="email" name="email" id="email" wire:model.defer="contact.email">
        <div class="validation-message">
            {{ $errors->first('contact.email') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contact.fields.email_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('contact.is_director') ? 'invalid' : '' }}">
        <input class="form-control" type="checkbox" name="is_director" id="is_director" wire:model.defer="contact.is_director">
        <label class="form-label inline ml-1" for="is_director">{{ trans('cruds.contact.fields.is_director') }}</label>
        <div class="validation-message">
            {{ $errors->first('contact.is_director') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contact.fields.is_director_helper') }}
        </div>
    </div>

    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>