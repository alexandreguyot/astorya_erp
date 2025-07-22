<div>
    <div class="overflow-hidden">
        <div class="overflow-x-auto">
            <div class="p-2">
                <a class="text-white btn bg-red-400" href="{{ route('admin.contracts.create', [$company])}}">Ajouter un contrat</a>
            </div>
            <table class="table w-full table-index">
                <thead >
                    <tr>
                        <th></th>
                        <th>Type</th>
                        <th>Périodicité</th>
                        <th>Date de fin</th>
                        <th>Dernière période facturée</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contracts as $contract)
                    @php
                        $firstDetail = $contract->contract_product_detail->first();
                        $typeTitle   = optional($firstDetail?->type_product?->type_contract)->title;
                        $lastPeriod  = $contract->bills->last()->last_bill_period ?? 'Pas encore facturé';
                    @endphp

                    <tr wire:click="toggle({{ $contract->id }})" class="hover:bg-gray-50 cursor-pointer {{ $openContractId === $contract->id ? 'bg-gray-100' : '' }}">
                        <td>
                            {{-- <input type="checkbox" value="{{ $contract->id }}" /> --}}
                        </td>
                        <td>
                            <span class="badge badge-blue">{{ $typeTitle }}</span>
                        </td>
                        <td>
                            <span class="badge badge-rose">{{ $contract->type_period->title }}</span>
                        </td>
                        <td>
                            <span class="badge badge-rose">{{ $contract->terminated_at }}</span>
                        </td>
                        <td>
                            <span class="badge badge-purple">{{ $lastPeriod }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.contracts.edit', $contract) }}"
                                class="btn btn-sm btn-success">
                                Modifier
                            </a>
                        </td>
                    </tr>
                    @if($openContractId === $contract->id)
                        <tr class="bg-gray-50">
                            <td colspan="6" class="p-0">
                                <div class="p-4">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-4 py-2 text-left">Article</th>
                                                    <th class="px-4 py-2 text-left">Réf. Article</th>
                                                    <th class="px-4 py-2 text-center">Début fact.</th>
                                                    <th class="px-4 py-2 text-center">Fin fact.</th>
                                                    <th class="px-4 py-2 text-center">Dernière fact.</th>
                                                    <th class="px-4 py-2 text-center">Qté</th>
                                                    <th class="px-4 py-2 text-right">PU mensuel HT</th>
                                                    {{-- <th class="px-4 py-2 text-center">Actions</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @forelse($contract->contract_product_detail as $detail)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-4 py-3">{{ $detail->designation }}</td>
                                                        <td class="px-4 py-3">{{ $detail->type_product->code }}</td>
                                                        <td class="px-4 py-3 text-center">{{ $detail->billing_started_at }}</td>
                                                        <td class="px-4 py-3 text-center">{{ $detail->billing_terminated_at }}</td>
                                                        <td class="px-4 py-3 text-center">
                                                            {{ optional($detail->last_billed_at)
                                                                ->format(config('project.date_format')) }}
                                                        </td>
                                                        <td class="px-4 py-3 text-center">{{ $detail->quantity }}</td>
                                                        <td class="px-4 py-3 text-right">
                                                            {{ number_format($detail->monthly_unit_price_without_taxe, 2, ',', ' ') }} €
                                                        </td>
                                                        {{-- <td class="px-4 py-3 text-center">
                                                            <button wire:click="editDetail({{ $detail->id }})" class="btn btn-sm btn-success">
                                                                Modifier
                                                            </button>
                                                        </td> --}}
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                                                            Aucun article n’est attaché à ce contrat.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center">Aucun contrat trouvé.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
