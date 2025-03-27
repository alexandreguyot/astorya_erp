<div>
    <div class="card-controls sm:flex">
        <div class="w-full sm:w-1/2">
            Per page:
            <select wire:model="perPage" class="form-select w-full sm:w-1/6">
                @foreach($paginationOptions as $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                @endforeach
            </select>

            @can('bill_delete')
                <button class="btn btn-rose ml-3 disabled:opacity-50 disabled:cursor-not-allowed" type="button" wire:click="confirm('deleteSelected')" wire:loading.attr="disabled" {{ $this->selectedCount ? '' : 'disabled' }}>
                    {{ __('Delete Selected') }}
                </button>
            @endcan

            @if(file_exists(app_path('Http/Livewire/ExcelExport.php')))
                <livewire:excel-export model="Bill" format="csv" />
                <livewire:excel-export model="Bill" format="xlsx" />
                <livewire:excel-export model="Bill" format="pdf" />
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
                            {{ trans('cruds.bill.fields.id') }}
                            @include('components.table.sort', ['field' => 'id'])
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.no_bill') }}
                            @include('components.table.sort', ['field' => 'no_bill'])
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.amount') }}
                            @include('components.table.sort', ['field' => 'amount'])
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.amount_vat_included') }}
                            @include('components.table.sort', ['field' => 'amount_vat_included'])
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.one_bill_per_period') }}
                            @include('components.table.sort', ['field' => 'one_bill_per_period'])
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.started_at') }}
                            @include('components.table.sort', ['field' => 'started_at'])
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.billed_at') }}
                            @include('components.table.sort', ['field' => 'billed_at'])
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.generated_at') }}
                            @include('components.table.sort', ['field' => 'generated_at'])
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.validated_at') }}
                            @include('components.table.sort', ['field' => 'validated_at'])
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.sent_at') }}
                            @include('components.table.sort', ['field' => 'sent_at'])
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.to_be_collected') }}
                            @include('components.table.sort', ['field' => 'to_be_collected'])
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.collected_at') }}
                            @include('components.table.sort', ['field' => 'collected_at'])
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.recorded_at') }}
                            @include('components.table.sort', ['field' => 'recorded_at'])
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.file_path') }}
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.company') }}
                            @include('components.table.sort', ['field' => 'company.name'])
                        </th>
                        <th>
                            {{ trans('cruds.company.fields.address') }}
                            @include('components.table.sort', ['field' => 'company.address'])
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.type_period') }}
                            @include('components.table.sort', ['field' => 'type_period.title'])
                        </th>
                        <th>
                            {{ trans('cruds.periodType.fields.nb_month') }}
                            @include('components.table.sort', ['field' => 'type_period.nb_month'])
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                        <tr>
                            <td>
                                <input type="checkbox" value="{{ $bill->id }}" wire:model="selected">
                            </td>
                            <td>
                                {{ $bill->id }}
                            </td>
                            <td>
                                {{ $bill->no_bill }}
                            </td>
                            <td>
                                {{ $bill->amount }}
                            </td>
                            <td>
                                {{ $bill->amount_vat_included }}
                            </td>
                            <td>
                                <input class="disabled:opacity-50 disabled:cursor-not-allowed" type="checkbox" disabled {{ $bill->one_bill_per_period ? 'checked' : '' }}>
                            </td>
                            <td>
                                {{ $bill->started_at }}
                            </td>
                            <td>
                                {{ $bill->billed_at }}
                            </td>
                            <td>
                                {{ $bill->generated_at }}
                            </td>
                            <td>
                                {{ $bill->validated_at }}
                            </td>
                            <td>
                                {{ $bill->sent_at }}
                            </td>
                            <td>
                                <input class="disabled:opacity-50 disabled:cursor-not-allowed" type="checkbox" disabled {{ $bill->to_be_collected ? 'checked' : '' }}>
                            </td>
                            <td>
                                {{ $bill->collected_at }}
                            </td>
                            <td>
                                {{ $bill->recorded_at }}
                            </td>
                            <td>
                                @foreach($bill->file_path as $key => $entry)
                                    <a class="link-light-blue" href="{{ $entry['url'] }}">
                                        <i class="far fa-file">
                                        </i>
                                        {{ $entry['file_name'] }}
                                    </a>
                                @endforeach
                            </td>
                            <td>
                                @if($bill->company)
                                    <span class="badge badge-relationship">{{ $bill->company->name ?? '' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($bill->company)
                                    {{ $bill->company->address ?? '' }}
                                @endif
                            </td>
                            <td>
                                @if($bill->typePeriod)
                                    <span class="badge badge-relationship">{{ $bill->typePeriod->title ?? '' }}</span>
                                @endif
                            </td>
                            <td>
                                @if($bill->typePeriod)
                                    {{ $bill->typePeriod->nb_month ?? '' }}
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    @can('bill_show')
                                        <a class="btn btn-sm btn-info mr-2" href="{{ route('admin.bills.show', $bill) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan
                                    @can('bill_edit')
                                        <a class="btn btn-sm btn-success mr-2" href="{{ route('admin.bills.edit', $bill) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('bill_delete')
                                        <button class="btn btn-sm btn-rose mr-2" type="button" wire:click="confirm('delete', {{ $bill->id }})" wire:loading.attr="disabled">
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
            {{ $bills->links() }}
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