<div>
    <div class="card-controls justify-between sm:flex mb-4">
        <div class="flex space-x-4 font-semibold">
            <div>
                Recherche :
                <input type="text"
                       wire:model.debounce.300ms="search"
                       class="form-control w-full" />
            </div>
            <div>
                <label class="block font-semibold">Mois :</label>
                <input type="month"
                       wire:model="dateStartMonth"
                       class="form-control" />
            </div>
        </div>
        <div class="flex items-center">
            <label class="mr-2">Afficher :</label>
            <select wire:model="perPage" class="form-control w-20">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
            <span class="ml-2">par page</span>
        </div>
    </div>

    <div class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-index w-full">
                <thead>
                    <tr>
                        <th></th>
                        <th class="w-1/6">
                            {{ trans('cruds.contract.fields.company') }}
                        </th>
                        <th class="w-1/6">
                            Période de facturation
                        </th>
                        <th>
                            Type de contrat
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
                                $groupKey = md5($companyName . $date . implode('-', $contracts->pluck('id')->toArray()));
                                $total = $contracts->sum(fn($contract) =>
                                    $contract->calculateTotalPrice(
                                        Carbon\Carbon::createFromFormat(config('project.date_format'), $dateStart)
                                    )
                                );
                            @endphp
                            <tr wire:poll.10s="isProcessingRow('{{ $groupKey }}')" class="hover:bg-gray-200">
                                <td>
                                    <input type="checkbox" wire:model="selectedContracts" value="{{ json_encode([
                                        'company' => $companyName,
                                        'contracts' => $contractIds,
                                        'date' => $date,
                                        'groupKey' => $groupKey,
                                    ]) }}">
                                </td>
                                <td class="">
                                    <a href="{{ route('admin.companies.edit', $contracts->first()->company_id )}}">{{ $companyName }}</a>
                                </td>
                                <td class="">
                                    {{ $date }}
                                </td>
                                <td>
                                    @foreach($contracts as $contract)
                                        @foreach($contract->contract_product_detail as $key => $detail)
                                            @if($detail->type_product && $key == 0)
                                                <span class="">
                                                    {{ $detail->type_product->type_contract->title ?? '' }}
                                                </span>
                                            @endif
                                        @endforeach
                                    @endforeach
                                </td>
                                <td class="">
                                    {{ number_format($total, 2, ',', '') }} €
                                </td>
                                <td class="w-1/4">
                                    <div class="flex justify-end">

                                        @if($this->isProcessingRow($groupKey))
                                            <svg class="animate-spin h-5 w-5 text-blue-600 text-left" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                            </svg>
                                        @else
                                            <a class="btn btn-sm btn-success mr-2"
                                            href="{{ route('admin.contracts.pdf.preview', [
                                                    'company' => $companyName,
                                                    'period' => str_replace('/', '-', str_replace(' au ', '-', $date)),
                                                    'contracts' => implode('-', $contracts->pluck('id')->toArray())
                                            ]) }}" target="_blank">
                                                Prévisualiser
                                            </a>
                                            <button class="btn btn-sm btn-success"
                                                wire:click="generateBill(@js($companyName), @js(implode('-', $contracts->pluck('id')->toArray())), @js($date))">
                                                Générer la facture
                                            </button>
                                        @endif
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
