<div>
    <div class="card-controls justify-between sm:flex mb-4">
        <div class="flex space-x-4 font-semibold">
            {{-- Recherche --}}
            <div>
                Recherche :
                <input
                    type="text"
                    wire:model.debounce.300ms="search"
                    placeholder="Recherche…"
                    class="inline-block w-full form-control shadow-2xl"
                />
            </div>

            {{-- Mois unique avec flèches --}}
            <div class="flex items-center space-x-4">
                <button
                    type="button"
                    wire:click="prevMonth"
                    class="px-3 py-1 mt-6 bg-gray-200 rounded hover:bg-gray-300"
                    title="Mois précédent"
                >&laquo;</button>

                <div>
                    <label for="dateMonth" class="block font-semibold mb-1">Mois :</label>
                    <input
                        id="dateMonth"
                        type="month"
                        wire:model="dateStartMonth"
                        class="form-control"
                    />
                </div>

                <button
                    type="button"
                    wire:click="nextMonth"
                    class="px-3 py-1 mt-6 bg-gray-200 rounded hover:bg-gray-300"
                    title="Mois suivant"
                >&raquo;</button>
            </div>
        </div>

        <div class="flex items-center">
            <label for="perPage" class="mr-2">Afficher :</label>
            <select id="perPage" wire:model="perPage" class="form-control w-20">
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
            </select>
            <span class="ml-2">par page</span>
        </div>
    </div>

    <div class="overflow-visible">
        <div class="overflow-x-auto [overflow-y:visible]">
            <table class="table table-index w-full">
                <thead>
                    <tr>
                        <th></th>
                        <th class="w-1/6">{{ trans('cruds.contract.fields.company') }}</th>
                        <th class="w-1/6">Période</th>
                        <th>Type de contrat</th>
                        <th>Montant HT</th>
                        <th class="flex justify-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dueContracts as $contract)
                        @php
                            $company   = $contract->company->name;
                            $period    = $contract->billing_period;
                            $total     = $contract->calculateTotalPrice(\Carbon\Carbon::createFromFormat(config('project.date_format'), $dateStart));
                            $id        = $contract->id;
                            $key       = md5($company.$period.$id);
                            $detail    = $contract->contract_product_detail->first();
                            $typeTitle = optional($detail?->type_product?->type_contract)->title;

                            // Prépare la liste "Désignation : Prix €"
                            $items = $contract->contract_product_detail
                                ->map(fn($d) => sprintf(
                                     "%s : %s €",
                                     $d->designation,
                                     number_format($d->monthly_unit_price_without_taxe,2,',','')
                                ))
                                ->unique()->values()->toArray();
                            $tooltip = implode("\n", $items);
                        @endphp

                        <tr wire:poll.10s="isProcessingRow('{{ $key }}')" class="hover:bg-gray-200">
                            <td>
                                <input type="checkbox"
                                       wire:model="selectedContracts"
                                       value="{{ $id }}" />
                            </td>
                            <td>
                                <a href="{{ route('admin.companies.edit', $contract->company_id) }}">
                                    {{ $company }}
                                </a>
                            </td>
                            <td>{{ $period }}</td>
                            <td class="relative overflow-visible">
                                <span class="badge badge-blue">{{ $typeTitle }}</span>
                                <span class="inline-block ml-1 group cursor-pointer">
                                    ℹ️
                                    <div
                                      class="invisible opacity-0
                                             group-hover:visible group-hover:opacity-100
                                             transition-opacity duration-200
                                             fixed z-50
                                             bg-gray-700 text-white text-sm p-2 rounded shadow-lg
                                             whitespace-pre-line max-w-xs"
                                    >{{ $tooltip }}</div>
                                </span>
                            </td>
                            <td>{{ number_format($total,2,',','') }} €</td>
                            <td class="flex justify-end space-x-2">
                                @if($this->isProcessingRow($key))
                                    <svg class="animate-spin h-5 w-5 text-blue-600 inline-block" viewBox="0 0 24 24">
                                      <circle class="opacity-25" cx="12" cy="12" r="10"
                                              stroke="currentColor" stroke-width="4" fill="none"/>
                                      <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8v8z"/>
                                    </svg>
                                @else
                                    <a class="btn btn-sm btn-success mr-2"
                                       href="{{ route('admin.contracts.pdf.preview', [
                                           'company'   => $company,
                                           'period'    => str_replace('/', '-', $period),
                                           'contracts' => $id,
                                       ]) }}"
                                       target="_blank">
                                        Prévisualiser
                                    </a>
                                    {{-- <button class="btn btn-sm btn-success"
                                            wire:click="validateGroup('{{ $company }}','{{ $id }}','{{ $period }}')">
                                        Valider
                                    </button> --}}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center">Aucune entrée trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- === PAGINATION === --}}
    <div class="card-body mt-4">
        {{ $dueContracts->links() }}
    </div>
</div>
