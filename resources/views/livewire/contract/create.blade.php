<form wire:submit.prevent="submit" class="grid grid-cols-2 gap-2 pt-3">

    <div class="form-group {{ $errors->has('contract.company_id') ? 'invalid' : '' }} col-span-full">
        <label class="form-label" for="company">{{ trans('cruds.contract.fields.company') }}</label>
        <x-select-list class="form-control" disabled id="company" name="company" :options="$this->listsForFields['company']" wire:model="contract.company_id" />
        <div class="validation-message">
            {{ $errors->first('contract.company_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contract.fields.company_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('contract.type_contract_id') ? 'invalid' : '' }}">
        <label class="form-label required">Type de contrat</label>
        <x-select-list class="form-control required" id="type_contract_id" name="type_contract_id" :options="$this->listsForFields['type_contracts']" wire:model="selectedTypeContractId" />
        <div class="validation-message">
            {{ $errors->first('contract.type_contract_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contract.fields.type_contract_id_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('contract.type_period_id') ? 'invalid' : '' }}">
        <label class="form-label required" for="type_periods">Périodicité</label>
        <x-select-list class="form-control" required id="type_periods" name="type_periods" :options="$this->listsForFields['type_periods']" wire:model="contract.type_period_id" />
        <div class="validation-message">
            {{ $errors->first('contract.type_period_id') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contract.fields.type_period_id_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('contract.setup_at') ? 'invalid' : '' }}">
        <label class="form-label required" for="setup_at">{{ trans('cruds.contract.fields.setup_at') }}</label>
        <x-date-picker class="form-control" required wire:model="contract.setup_at" id="setup_at" name="setup_at" picker="date" />
        <div class="validation-message">
            {{ $errors->first('contract.setup_at') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contract.fields.setup_at_helper') }}
        </div>
    </div>
    <div class="form-group {{ $errors->has('contract.terminated_at') ? 'invalid' : '' }}">
        <label class="form-label" for="terminated_at">{{ trans('cruds.contract.fields.terminated_at') }}</label>
        <x-date-picker class="form-control" wire:model="contract.terminated_at" id="terminated_at" name="terminated_at" picker="date"  />
        <div class="validation-message">
            {{ $errors->first('contract.terminated_at') }}
        </div>
        <div class="help-block">
            {{ trans('cruds.contract.fields.terminated_at_helper') }}
        </div>
    </div>
    @if($selectedTypeContractId)
        <h4 class="text-lg font-semibold mb-2 col-span-full">Article lié au contrat</h4>

        <div class="form-group {{ $errors->has('selectedProduct.type_product_id') ? 'invalid' : '' }}">
            <label class="form-label required" for="type_product_id">Article</label>
            <x-select-list class="form-control" id="type_product_id" name="type_product_id"
                :options="$this->filteredProducts"
                wire:model="selectedProduct.type_product_id" />
            <div class="validation-message">
                {{ $errors->first('selectedProduct.type_product_id') }}
            </div>
        </div>

        <div class="form-group">
            <label class="form-label required">Description produit</label>
            <input type="text" class="form-control" required wire:model="selectedProduct.designation">
        </div>

        <div class="form-group">
            <label class="form-label">Quantité</label>
            <input type="number" class="form-control" wire:model="selectedProduct.quantity">
        </div>
        <div class="form-group">
            <label class="form-label">Observation</label>
            <input type="number" class="form-control" wire:model="selectedProduct.capacity">
        </div>

        <div class="form-group">
            <label class="form-label">Prix unitaire HT</label>
            <input type="text" class="form-control" wire:model="selectedProduct.monthly_unit_price_without_taxe">
        </div>
        <div class="form-group">
            <label class="form-label">Prix unitaire TTC</label>
            <input type="text" class="form-control" wire:model="selectedProduct.monthly_unit_price_with_taxe">
        </div>
    @endif


    <div class="form-group">
        <button class="btn btn-indigo mr-2" type="submit">
            {{ trans('global.save') }}
        </button>
        <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary">
            {{ trans('global.cancel') }}
        </a>
    </div>
</form>
