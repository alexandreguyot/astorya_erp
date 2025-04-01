<div>
    <div class="card-controls sm:flex ">
        <div class="flex space-x-4 font-semibold">
            <div>
                Recherche:
                <input type="text" wire:model.debounce.300ms="search" class="inline-block w-full form-control shadow-2xl" />
            </div>
            <div>
                Date de début :
                <x-date-picker wire:model="dateStart" id="dateStart" class="border rounded shadow-2xl px-2 py-1" placeholder="Date de début" picker="date"/>
            </div>
            <div>
                Date de fin :
                <x-date-picker wire:model="dateEnd" id="dateEnd" class="border rounded shadow-2xl px-2 py-1" placeholder="Date de fin" picker="date"/>
            </div>
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
                        <th>
                            {{ trans('cruds.bill.fields.company') }}
                            @include('components.table.sort', ['field' => 'company.name'])
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
                            {{ trans('cruds.bill.fields.generated_at') }}
                            @include('components.table.sort', ['field' => 'generated_at'])
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.sent_at') }}
                            @include('components.table.sort', ['field' => 'sent_at'])
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                        <tr>
                            <td>
                                @if($bill->company)
                                    <span class="badge badge-relationship">{{ $bill->company->name ?? '' }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $bill->no_bill }}
                            </td>
                            <td>
                                {{ number_format((float)$bill->amount, 2, ',', ''); }} €
                            </td>
                            <td>
                                {{ $bill->generated_at }}
                            </td>
                            <td>
                                {{ $bill->sent_at }}
                            </td>
                            <td>
                                <div class="flex justify-end">
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
