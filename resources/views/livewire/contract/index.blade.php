<div>
    <div class="card-controls justify-between sm:flex">
        <div class="flex space-x-4 font-semibold">
            <div>
                Recherche:
                <input type="text" wire:model.debounce.300ms="search" class="inline-block w-full form-control shadow-2xl" />
            </div>
            <div>
                Date de début :
                <x-month-picker wire:model="dateStartView" id="dateStartView" class="border rounded shadow-2xl px-2 py-1" placeholder="Date de début"/>
            </div>
            <div>
                Date de fin :
                <x-month-picker wire:model="dateEndView" id="dateEndView" class="border rounded shadow-2xl px-2 py-1" placeholder="Date de fin"/>
            </div>
        </div>
        <div class="font-semibold flex justify-end">
            <div>
                <label for="perPage" class="mr-2">Afficher :</label>
                <select wire:model="perPage" id="perPage" class="border rounded px-2 py-1 w-16">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span>éléments par page</span>
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
                        <th></th>
                        <th class="w-1/6">
                            {{ trans('cruds.contract.fields.company') }}
                            @include('components.table.sort', ['field' => 'company.name'])
                        </th>
                        <th class="w-1/6">
                            Période de facturation
                        </th>
                        <th>
                            Type de contract
                        </th>
                        <th>
                            Montant HT
                        </th>
                        <th class="flex justify-end">
                            <button class="btn btn-sm btn-info mr-2" wire:click="generateSelectedBills">
                                Générer les factures sélectionnées
                            </button>
                            <button class="btn btn-sm btn-info mr-2" wire:click="generateAllBills">
                                Générer toutes les factures
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groupedContracts as $companyName => $contractsByDate)
                        @foreach($contractsByDate as $date => $contracts)
                            @php
                                $contractIds = $contracts->pluck('id')->toArray();
                                $groupKey = md5($companyName . $date . implode('-', $contractIds)); // identifiant unique
                            @endphp
                            <tr class="hover:bg-gray-200">
                                <td>
                                    <input type="checkbox" wire:model="selectedContracts" value="{{ json_encode([
                                        'company' => $companyName,
                                        'contracts' => $contractIds,
                                        'date' => $date
                                    ]) }}">
                                </td>
                                <td class="text-blue-500 font-medium">
                                    {{ $companyName }}
                                </td>
                                <td class="text-green-500 font-semibold">
                                    {{ $date }}
                                </td>
                                <td>
                                    @foreach($contracts as $contract)
                                        @foreach($contract->contract_product_detail as $key => $detail)
                                            @if($detail->type_product && $key == 0)
                                                <span class="badge badge-red mr-1">
                                                    {{ $detail->type_product->type_contract->title ?? '' }}
                                                </span>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </td>
                                <td class="text-red-600 font-semibold">
                                    {{ number_format($contracts->sum(fn($contract) => floatval($contract->total_price)), 2, ',', ' ') }} €
                                </td>
                                <td class="w-1/4">
                                    <div class="flex justify-end">
                                        <a class="btn btn-sm btn-success mr-2"
                                           href="{{ route('admin.contracts.pdf.preview', [
                                                'company' => $companyName,
                                                'period' => str_replace('/', '-', str_replace(' au ', '-', $date)),
                                                'contracts' => implode('-', $contracts->pluck('id')->toArray())
                                           ]) }}" target="_blank">
                                            Prévisualiser
                                        </a>
                                        <button class="btn btn-sm btn-success"
                                                wire:click="generateBill('{{ $companyName }}', '{{ implode('-', $contracts->pluck('id')->toArray()) }}', '{{ $date }}')">
                                            Générer la facture
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="5">Aucune entrée trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
    <div class="card-body">
        <div class="pt-3">
            {{ $groupedContracts->links() }}
        </div>
    </div>
</div>
