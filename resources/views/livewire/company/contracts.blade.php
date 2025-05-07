<div>
    <div class="overflow-hidden">
        <div class="overflow-x-auto">
            <div class="p-2">
                <a class="text-white btn bg-red-400" href="{{ route('admin.contracts.create', [$company])}}">Ajouter un contrat</a>
            </div>
            <table class="table w-full table-index">
                <thead>
                    <tr>
                        <th>
                            Type
                        </th>
                        <th>
                            Périodicité
                        </th>
                        <th>
                            Dernière période facturée
                        </th>
                        <th>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $contract)
                            <tr>
                                <td>
                                    <div>
                                        @foreach($contract->contract_product_detail as $key => $detail)
                                            @if($detail->type_product && $key == 0)
                                                <span class="badge badge-blue">
                                                    {{ $detail->type_product->type_contract->title ?? '' }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <div class="badge badge-rose">
                                        {{ $contract->type_period->title }}
                                    </div>
                                </td>
                                <td>
                                    <div class="badge badge-purple">
                                        {{ $contract->bills->last()->last_bill_period ?? 'Pas encore facturé' }}
                                    </div>
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-success mr-2" href="{{ route('admin.contracts.edit', $contract) }}">
                                        Modifier
                                    </a>
                                </td>
                            </tr>
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
