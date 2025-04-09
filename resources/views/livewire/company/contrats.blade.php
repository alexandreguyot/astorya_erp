<div>
    <div class="overflow-hidden">
        <div class="overflow-x-auto">
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
                                        {{ $contract->bills->last()->last_bill_period ?? '' }}
                                    </div>
                                </td>
                                <td>
                                    @if (!$contract->isActive())
                                        <button class="btn btn-sm btn-success mr-2" wire:click="confirm('delete', {{ $contract->id }})">
                                            Modifier
                                        </button>
                                    @endif
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
</div>
