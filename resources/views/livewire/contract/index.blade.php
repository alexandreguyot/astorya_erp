<div>
    <div class="card-controls sm:flex">
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
                            {{ trans('cruds.contract.fields.company') }}
                            @include('components.table.sort', ['field' => 'company.name'])
                        </th>
                        <th>
                            Type de contract
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
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $contract)
                        <tr>
                            <td>
                                @if($contract->company)
                                    <span class="badge badge-relationship">{{ $contract->company->name ?? '' }}</span>
                                @endif
                            </td>
                            <td>
                                @foreach($contract->contractProductDetails as $detail)
                                    @if($detail->product)
                                        <span class="badge badge-relationship">
                                            {{ $detail->product->designation_short ?? '' }}
                                        </span>
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                {{ $contract->started_at }}
                            </td>
                            <td>
                                {{ $contract->terminated_at }}
                            </td>
                            <td>
                                <div class="flex justify-end">
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
