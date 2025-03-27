<div>
    <div class="card-controls sm:flex">
        <div class="w-full sm:w-1/2">
            Per page:
            <select wire:model="perPage" class="form-select w-full sm:w-1/6">
                @foreach($paginationOptions as $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                @endforeach
            </select>

            @can('bank_account_delete')
                <button class="btn btn-rose ml-3 disabled:opacity-50 disabled:cursor-not-allowed" type="button" wire:click="confirm('deleteSelected')" wire:loading.attr="disabled" {{ $this->selectedCount ? '' : 'disabled' }}>
                    {{ __('Delete Selected') }}
                </button>
            @endcan

            @if(file_exists(app_path('Http/Livewire/ExcelExport.php')))
                <livewire:excel-export model="BankAccount" format="csv" />
                <livewire:excel-export model="BankAccount" format="xlsx" />
                <livewire:excel-export model="BankAccount" format="pdf" />
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
                            {{ trans('cruds.bankAccount.fields.id') }}
                            @include('components.table.sort', ['field' => 'id'])
                        </th>
                        <th>
                            {{ trans('cruds.bankAccount.fields.no_rum') }}
                            @include('components.table.sort', ['field' => 'no_rum'])
                        </th>
                        <th>
                            {{ trans('cruds.bankAccount.fields.effective_start_date') }}
                            @include('components.table.sort', ['field' => 'effective_start_date'])
                        </th>
                        <th>
                            {{ trans('cruds.bankAccount.fields.bic') }}
                            @include('components.table.sort', ['field' => 'bic'])
                        </th>
                        <th>
                            {{ trans('cruds.bankAccount.fields.iban') }}
                            @include('components.table.sort', ['field' => 'iban'])
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bankAccounts as $bankAccount)
                        <tr>
                            <td>
                                <input type="checkbox" value="{{ $bankAccount->id }}" wire:model="selected">
                            </td>
                            <td>
                                {{ $bankAccount->id }}
                            </td>
                            <td>
                                {{ $bankAccount->no_rum }}
                            </td>
                            <td>
                                {{ $bankAccount->effective_start_date }}
                            </td>
                            <td>
                                {{ $bankAccount->bic }}
                            </td>
                            <td>
                                {{ $bankAccount->iban }}
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    @can('bank_account_show')
                                        <a class="btn btn-sm btn-info mr-2" href="{{ route('admin.bank-accounts.show', $bankAccount) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan
                                    @can('bank_account_edit')
                                        <a class="btn btn-sm btn-success mr-2" href="{{ route('admin.bank-accounts.edit', $bankAccount) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('bank_account_delete')
                                        <button class="btn btn-sm btn-rose mr-2" type="button" wire:click="confirm('delete', {{ $bankAccount->id }})" wire:loading.attr="disabled">
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
            {{ $bankAccounts->links() }}
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