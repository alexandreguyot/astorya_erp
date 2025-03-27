<div>
    <div class="card-controls sm:flex">
        <div class="w-full sm:w-1/2">
            Per page:
            <select wire:model="perPage" class="form-select w-full sm:w-1/6">
                @foreach($paginationOptions as $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                @endforeach
            </select>

            @can('owner_delete')
                <button class="btn btn-rose ml-3 disabled:opacity-50 disabled:cursor-not-allowed" type="button" wire:click="confirm('deleteSelected')" wire:loading.attr="disabled" {{ $this->selectedCount ? '' : 'disabled' }}>
                    {{ __('Delete Selected') }}
                </button>
            @endcan

            @if(file_exists(app_path('Http/Livewire/ExcelExport.php')))
                <livewire:excel-export model="Owner" format="csv" />
                <livewire:excel-export model="Owner" format="xlsx" />
                <livewire:excel-export model="Owner" format="pdf" />
            @endif




        </div>
        <div class="w-full sm:w-1/2 sm:text-right">
            Search:
            <input type="text" wire:model.debounce.300ms="search" class="w-full sm:w-1/3 inline-block" />
        </div>
    </div>
    <div wire:loading.delay>
        Loading...
    </div>

    <div class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-index w-full">
                <thead>
                    <tr>
                        <th class="w-9">
                        </th>
                        <th class="w-28">
                            {{ trans('cruds.owner.fields.id') }}
                            @include('components.table.sort', ['field' => 'id'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.name') }}
                            @include('components.table.sort', ['field' => 'name'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.address') }}
                            @include('components.table.sort', ['field' => 'address'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.zip_code') }}
                            @include('components.table.sort', ['field' => 'zip_code'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.city') }}
                            @include('components.table.sort', ['field' => 'city'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.email') }}
                            @include('components.table.sort', ['field' => 'email'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.phone') }}
                            @include('components.table.sort', ['field' => 'phone'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.web_site_address') }}
                            @include('components.table.sort', ['field' => 'web_site_address'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.siret') }}
                            @include('components.table.sort', ['field' => 'siret'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.capital') }}
                            @include('components.table.sort', ['field' => 'capital'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.bic') }}
                            @include('components.table.sort', ['field' => 'bic'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.iban') }}
                            @include('components.table.sort', ['field' => 'iban'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.hotline_name') }}
                            @include('components.table.sort', ['field' => 'hotline_name'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.hotline_phone') }}
                            @include('components.table.sort', ['field' => 'hotline_phone'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.hotline_email') }}
                            @include('components.table.sort', ['field' => 'hotline_email'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.accounting_manager') }}
                            @include('components.table.sort', ['field' => 'accounting_manager'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.accounting_phone') }}
                            @include('components.table.sort', ['field' => 'accounting_phone'])
                        </th>
                        <th>
                            {{ trans('cruds.owner.fields.accounting_email') }}
                            @include('components.table.sort', ['field' => 'accounting_email'])
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($owners as $owner)
                        <tr>
                            <td>
                                <input type="checkbox" value="{{ $owner->id }}" wire:model="selected">
                            </td>
                            <td>
                                {{ $owner->id }}
                            </td>
                            <td>
                                {{ $owner->name }}
                            </td>
                            <td>
                                {{ $owner->address }}
                            </td>
                            <td>
                                {{ $owner->zip_code }}
                            </td>
                            <td>
                                {{ $owner->city }}
                            </td>
                            <td>
                                {{ $owner->email }}
                            </td>
                            <td>
                                {{ $owner->phone }}
                            </td>
                            <td>
                                {{ $owner->web_site_address }}
                            </td>
                            <td>
                                {{ $owner->siret }}
                            </td>
                            <td>
                                {{ $owner->capital }}
                            </td>
                            <td>
                                {{ $owner->bic }}
                            </td>
                            <td>
                                {{ $owner->iban }}
                            </td>
                            <td>
                                {{ $owner->hotline_name }}
                            </td>
                            <td>
                                {{ $owner->hotline_phone }}
                            </td>
                            <td>
                                {{ $owner->hotline_email }}
                            </td>
                            <td>
                                {{ $owner->accounting_manager }}
                            </td>
                            <td>
                                {{ $owner->accounting_phone }}
                            </td>
                            <td>
                                {{ $owner->accounting_email }}
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    @can('owner_show')
                                        <a class="btn btn-sm btn-info mr-2" href="{{ route('admin.owners.show', $owner) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan
                                    @can('owner_edit')
                                        <a class="btn btn-sm btn-success mr-2" href="{{ route('admin.owners.edit', $owner) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('owner_delete')
                                        <button class="btn btn-sm btn-rose mr-2" type="button" wire:click="confirm('delete', {{ $owner->id }})" wire:loading.attr="disabled">
                                            {{ trans('global.delete') }}
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10">No entries found.</td>
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
            {{ $owners->links() }}
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