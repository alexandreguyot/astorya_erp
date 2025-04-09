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
                            Période de facturation
                        </th>
                        <th>
                            Type de contract
                        </th>
                        <th>
                            Montant HT
                        </th>
                        <th></th>
                        <th class="flex justify-end">
                            <button class="btn btn-sm btn-info mr-2" wire:click="generateAllBills">
                                Générer toutes les factures
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groupedContracts as $companyName => $contractsByDate)
                        <tr>
                            <td colspan="6" class="bg-blue-100 text-blue-500 font-medium">
                                {{ $companyName }}
                            </td>
                        </tr>

                        @foreach($contractsByDate as $date => $contracts)
                            <tr class="bg-green-100">
                                <td>
                                </td>
                                <td colspan="4" class="font-semibold text-green-500">
                                    {{ $date }}
                                </td>
                                <td>
                                    <div class="flex justify-end">
                                        <a class="btn btn-sm btn-success mr-2" href="{{ route('admin.contracts.pdf.preview', [
                                            'company' => $companyName,
                                            'period' => str_replace('/', '-', str_replace(' au ', '-', $date)),
                                            'contracts' => implode('-', $contracts->pluck('id')->toArray())
                                        ]) }}" target="_blank">
                                            Prévisualiser le PDF
                                        </a>
                                        <button class="btn btn-sm btn-success mr-2"
                                                wire:click="generateBill('{{ $companyName }}', '{{ implode('-', $contracts->pluck('id')->toArray()) }}', '{{ $date }}')">
                                            Générer la facture
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @foreach($contracts as $contract)
                                <tr class="bg-red-100">
                                    <td></td>
                                    <td></td>
                                    <td>
                                        @foreach($contract->contract_product_detail as $key => $detail)
                                            @if($detail->type_product && $key == 0)
                                                <span class="badge badge-red">
                                                    {{ $detail->type_product->type_contract->title ?? '' }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="text-red-600 font-semibold">
                                        {{ $contract->total_price }} €
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforeach
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="10">Aucune entrée trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
