<div>
    <div class="card-controls sm:flex">
        <div class="w-full sm:w-1/2 ">
            Recherche:
            <input type="text" wire:model.debounce.300ms="search" class="inline-block w-full sm:w-1/2 form-control" />
        </div>
        <div class="w-full sm:w-1/2 sm:text-right">
        </div>
    </div>
    <div wire:loading.delay>
        Chargement...
    </div>

    <div class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-index w-full">
                <thead>
                    <tr>
                        <th class="w-9">
                        </th>
                        <th class="w-28">
                            {{ trans('cruds.company.fields.id') }}
                            @include('components.table.sort', ['field' => 'id'])
                        </th>
                        <th>
                            {{ trans('cruds.company.fields.name') }}
                            @include('components.table.sort', ['field' => 'name'])
                        </th>
                        <th>
                            {{ trans('cruds.company.fields.address') }}
                            @include('components.table.sort', ['field' => 'address'])
                        </th>
                        <th>
                            {{ trans('cruds.company.fields.address_compl') }}
                            @include('components.table.sort', ['field' => 'address_compl'])
                        </th>
                        <th>
                            {{ trans('cruds.company.fields.city') }}
                            @include('components.table.sort', ['field' => 'city.name'])
                        </th>
                        <th>
                            {{ trans('cruds.city.fields.zipcode') }}
                            @include('components.table.sort', ['field' => 'city.zipcode'])
                        </th>
                        <th>
                            {{ trans('cruds.company.fields.email') }}
                            @include('components.table.sort', ['field' => 'email'])
                        </th>
                        <th>
                            {{ trans('cruds.company.fields.accounting') }}
                            @include('components.table.sort', ['field' => 'accounting'])
                        </th>
                        <th>
                            {{ trans('cruds.company.fields.ciel_reference') }}
                            @include('components.table.sort', ['field' => 'ciel_reference'])
                        </th>
                        <th>
                            {{ trans('cruds.company.fields.send_bill_type') }}
                            @include('components.table.sort', ['field' => 'send_bill_type'])
                        </th>
                        <th>
                            {{ trans('cruds.company.fields.one_bill_per_period') }}
                            @include('components.table.sort', ['field' => 'one_bill_per_period'])
                        </th>
                        <th>
                            {{ trans('cruds.company.fields.bill_payment_methood') }}
                            @include('components.table.sort', ['field' => 'bill_payment_methood'])
                        </th>
                        <th>
                            {{ trans('cruds.company.fields.observations') }}
                            @include('components.table.sort', ['field' => 'observations'])
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($companies as $company)
                        <tr>
                            <td>
                                <input type="checkbox" value="{{ $company->id }}" wire:model="selected">
                            </td>
                            <td>
                                {{ $company->id }}
                            </td>
                            <td>
                                {{ $company->name }}
                            </td>
                            <td>
                                {{ $company->address }}
                            </td>
                            <td>
                                {{ $company->address_compl }}
                            </td>
                            <td>
                                @if($company->city)
                                    <span class="badge badge-relationship">{{ $company->city->name ?? '' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($company->city)
                                    {{ $company->city->zipcode ?? '' }}
                                @endif
                            </td>
                            <td>
                                <a class="link-light-blue" href="mailto:{{ $company->email }}">
                                    <i class="far fa-envelope fa-fw">
                                    </i>
                                    {{ $company->email }}
                                </a>
                            </td>
                            <td>
                                {{ $company->accounting }}
                            </td>
                            <td>
                                {{ $company->ciel_reference }}
                            </td>
                            <td>
                                <input class="disabled:opacity-50 disabled:cursor-not-allowed" type="checkbox" disabled {{ $company->send_bill_type ? 'checked' : '' }}>
                            </td>
                            <td>
                                <input class="disabled:opacity-50 disabled:cursor-not-allowed" type="checkbox" disabled {{ $company->one_bill_per_period ? 'checked' : '' }}>
                            </td>
                            <td>
                                {{ $company->bill_payment_methood }}
                            </td>
                            <td>
                                {{ $company->observations }}
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    @can('company_show')
                                        <a class="btn btn-sm btn-info mr-2" href="{{ route('admin.companies.show', $company) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan
                                    @can('company_edit')
                                        <a class="btn btn-sm btn-success mr-2" href="{{ route('admin.companies.edit', $company) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('company_delete')
                                        <button class="btn btn-sm btn-rose mr-2" type="button" wire:click="confirm('delete', {{ $company->id }})" wire:loading.attr="disabled">
                                            {{ trans('global.delete') }}
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10">Aucune entrée trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-body">
        <div class="pt-3">
            @if($this->selectedCount)
                <p class="text-sm leading-5">
                    <span class="font-medium">
                        {{ $this->selectedCount }}
                    </span>
                    {{ __('Entries selected') }}
                </p>
            @endif
            {{ $companies->links() }}
        </div>
    </div>
</div>

@push('scripts')
    <script>
        Livewire.on('confirm', e => {
    if (!confirm("{{ trans('global.areYouSure') }}")) {
        return
    }
@this[e.callback](...e.argv)
})
    </script>
@endpush
