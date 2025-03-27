<div>
    <div class="card-controls sm:flex">
        <div class="w-full sm:w-1/2">
            Per page:
            <select wire:model="perPage" class="form-select w-full sm:w-1/6">
                @foreach($paginationOptions as $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                @endforeach
            </select>

            @can('contract_delete')
                <button class="btn btn-rose ml-3 disabled:opacity-50 disabled:cursor-not-allowed" type="button" wire:click="confirm('deleteSelected')" wire:loading.attr="disabled" {{ $this->selectedCount ? '' : 'disabled' }}>
                    {{ __('Delete Selected') }}
                </button>
            @endcan

            @if(file_exists(app_path('Http/Livewire/ExcelExport.php')))
                <livewire:excel-export model="Contract" format="csv" />
                <livewire:excel-export model="Contract" format="xlsx" />
                <livewire:excel-export model="Contract" format="pdf" />
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
                            {{ trans('cruds.contract.fields.id') }}
                            @include('components.table.sort', ['field' => 'id'])
                        </th>
                        <th>
                            {{ trans('cruds.contract.fields.company') }}
                            @include('components.table.sort', ['field' => 'company.name'])
                        </th>
                        <th>
                            {{ trans('cruds.company.fields.address') }}
                            @include('components.table.sort', ['field' => 'company.address'])
                        </th>
                        <th>
                            {{ trans('cruds.contract.fields.setup_at') }}
                            @include('components.table.sort', ['field' => 'setup_at'])
                        </th>
                        <th>
                            {{ trans('cruds.contract.fields.established_at') }}
                            @include('components.table.sort', ['field' => 'established_at'])
                        </th>
                        <th>
                            {{ trans('cruds.contract.fields.started_at') }}
                            @include('components.table.sort', ['field' => 'started_at'])
                        </th>
                        <th>
                            {{ trans('cruds.contract.fields.terminated_at') }}
                            @include('components.table.sort', ['field' => 'terminated_at'])
                        </th>
                        <th>
                            {{ trans('cruds.contract.fields.billed_at') }}
                            @include('components.table.sort', ['field' => 'billed_at'])
                        </th>
                        <th>
                            {{ trans('cruds.contract.fields.validated_at') }}
                            @include('components.table.sort', ['field' => 'validated_at'])
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $contract)
                        <tr>
                            <td>
                                <input type="checkbox" value="{{ $contract->id }}" wire:model="selected">
                            </td>
                            <td>
                                {{ $contract->id }}
                            </td>
                            <td>
                                @if($contract->company)
                                    <span class="badge badge-relationship">{{ $contract->company->name ?? '' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($contract->company)
                                    {{ $contract->company->address ?? '' }}
                                @endif
                            </td>
                            <td>
                                {{ $contract->setup_at }}
                            </td>
                            <td>
                                {{ $contract->established_at }}
                            </td>
                            <td>
                                {{ $contract->started_at }}
                            </td>
                            <td>
                                {{ $contract->terminated_at }}
                            </td>
                            <td>
                                {{ $contract->billed_at }}
                            </td>
                            <td>
                                {{ $contract->validated_at }}
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    @can('contract_show')
                                        <a class="btn btn-sm btn-info mr-2" href="{{ route('admin.contracts.show', $contract) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan
                                    @can('contract_edit')
                                        <a class="btn btn-sm btn-success mr-2" href="{{ route('admin.contracts.edit', $contract) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('contract_delete')
                                        <button class="btn btn-sm btn-rose mr-2" type="button" wire:click="confirm('delete', {{ $contract->id }})" wire:loading.attr="disabled">
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
            {{ $contracts->links() }}
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