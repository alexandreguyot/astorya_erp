<div>
    <div class="card-controls sm:flex ">
        <div class="flex space-x-4 font-semibold">
            <div>
                Recherche:
                <input type="text" wire:model.debounce.300ms="search" class="inline-block w-full form-control shadow-2xl" />
            </div>
            <div>
                Date de début :
                <x-month-picker wire:model="dateStartView" id="dateStart" class="border rounded shadow-2xl px-2 py-1" placeholder="Date de début"/>
            </div>
            <div>
                Date de fin :
                <x-month-picker wire:model="dateEndView" id="dateEnd" class="border rounded shadow-2xl px-2 py-1" placeholder="Date de fin"/>
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
                        </th>
                        <th>
                            N° Facture
                        </th>
                        <th>
                            Montant HT
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.generated_at') }}
                        </th>
                        <th>
                            {{ trans('cruds.bill.fields.sent_at') }}
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($billGroups as $no_bill => $bill)
                        <tr>
                            <td>
                                <span class="badge badge-relationship">{{ $bill['company'] ?? '' }}</span>
                            </td>
                            <td>
                                <div class="badge badge-red">
                                    {{ $bill['no_bill'] ?? '' }}
                                </div>
                            </td>
                            <td>
                                <div class="badge badge-red">
                                    {{ number_format((float)$bill['total_ht'], 2, ',', ''); }} €
                                </div>
                            </td>
                            <td>
                                <div class="badge badge-purple">
                                    {{ $bill['generated_at'] ?? '' }}
                                </div>
                            </td>
                            <td>
                                <div class="badge badge-purple">
                                    {{ $bill['sent_at'] ?? '' }}
                                </div>
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    <a class="btn btn-sm btn-indigo mr-2" href="{{ route('admin.bills.pdf', $no_bill) }}">
                                        Télécharger la facture
                                    </a>
                                    <a class="btn btn-sm btn-indigo mr-2"  wire:click="sendMail({{ $no_bill }})">
                                        Envoyer la facture
                                    </a>
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
</div>

