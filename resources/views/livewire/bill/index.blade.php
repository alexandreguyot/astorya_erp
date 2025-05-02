<div>
    <div class="card-controls justify-between sm:flex ">
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
                        <th class="flex flex-col space-y-2 justify-end">
                            <button class="btn btn-sm btn-info mr-2" wire:loading.attr="disabled" wire:click="generateAllBills">
                                Envoyer les mails des factures séléctionnées
                            </button>
                            <button class="btn btn-sm btn-info mr-2" wire:loading.attr="disabled" wire:click="generateAllBills">
                                Envoyer tous les mails des factures
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($billGroups as $bill)
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
                                    <a class="btn btn-sm btn-indigo mr-2" href="{{ route('admin.bills.pdf', $bill['no_bill']) }}" target="_blank">
                                        Télécharger la facture
                                    </a>
                                    <a class="btn btn-sm btn-indigo mr-2"  wire:click="sendMail({{ $bill['no_bill'] }})">
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
    <div class="card-body">
        <div class="pt-3">
            {{ $billGroups->links() }}
        </div>
    </div>
    <div class="card-body">
        <div class="flex">
            <button class="btn btn-sm btn-info mr-2" wire:loading.attr="disabled" wire:click="downloadZipFile">
                Télécharger le fichier zip
            </button>
            <button class="btn btn-sm btn-info mr-2" wire:loading.attr="disabled" wire:click="generateComptableFile">
                Télécharger le fichier comptable
            </button>
            <button class="btn btn-sm btn-info mr-2" wire:loading.attr="disabled" wire:click="generateOrderFile">
                Télécharger le fichier ordre de prélevement
            </button>
        </div>
    </div>
</div>

