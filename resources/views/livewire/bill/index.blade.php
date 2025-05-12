<div>
    <div class="card-controls justify-between sm:flex ">
        <div class="flex space-x-4 font-semibold">
            <div>
                Recherche:
                <input type="text" wire:model.debounce.300ms="search" class="inline-block w-full form-control shadow-2xl" />
            </div>
            <div>
                <label for="dateStart" class="block font-semibold">Date de début :</label>
                <input
                    id="dateStart"
                    type="month"
                    wire:model="dateStartMonth"
                    class="form-control"
                />
            </div>
            <div>
                <label for="dateEnd" class="block font-semibold">Date de fin :</label>
                <input
                    id="dateEnd"
                    type="month"
                    wire:model="dateEndMonth"
                    class="form-control"
                />
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
    <div class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-index w-full">
                <thead>
                    <tr>
                        <th></th>
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
                            @php
                                $unsentCount = $billGroups->filter(fn($b) => is_null($b['sent_at']))->count();
                            @endphp

                            <button
                                class="btn btn-sm btn-info mr-2 disabled:opacity-60"
                                wire:loading.attr="disabled"
                                wire:click="sendSelectedBills"
                                {{ $unsentCount === 0 ? 'disabled' : '' }}
                                @disabled($unsentCount === 0)
                            >
                            Envoyer les mails des factures sélectionnées
                            </button>

                            <button
                                class="btn btn-sm btn-info mr-2 disabled:opacity-60"
                                wire:loading.attr="disabled"
                                wire:click="sendAllBills"
                                {{ $unsentCount === 0 ? 'disabled' : '' }}
                                @disabled($unsentCount === 0)
                            >
                            Envoyer tous les mails des factures
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($billGroups as $bill)
                        <tr>
                            <td>
                                <input type="checkbox" wire:model="selectedBills" value="{{ $bill['no_bill'] }}" class="form-checkbox" />
                            </td>
                            <td>
                                <a href="{{ route('admin.companies.edit', $bill['company_id'] )}}" class="">{{ $bill['company'] ?? '' }}</a>
                            </td>
                            <td>
                                <div class="">
                                    {{ $bill['no_bill'] ?? '' }}
                                </div>
                            </td>
                            <td>
                                <div class="">
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
                                <div class="flex justify-end" wire:poll.5s="isSending('{{ $bill['no_bill'] }}')">
                                    <a class="btn btn-sm btn-indigo mr-2" href="{{ route('admin.bills.pdf', ['bill' => $bill['no_bill'], 'dateStart' => Carbon\Carbon::createFromFormat('d/m/Y', $dateStart)->format('Y-m-d')]) }}" target="_blank">
                                        Télécharger la facture
                                    </a>
                                    @if($this->isSending($bill['no_bill']))
                                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                        </svg>
                                    @elseif(is_null($bill['sent_at']))
                                        <button class="btn btn-sm btn-indigo" wire:click="sendMail('{{ $bill['no_bill'] }}')" wire:loading.attr="disabled">
                                            Envoyer la facture
                                        </button>
                                    @endif
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
            <button wire:click="downloadZipFile" wire:loading.remove wire:target="downloadZipFile" class="btn btn-sm btn-info mr-2">
                Télécharger le fichier zip
            </button>

            <svg wire:loading wire:target="downloadZipFile" class="animate-spin h-5 w-5 text-blue-600 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>

            <button class="btn btn-sm btn-info mr-2" wire:loading.attr="disabled" wire:click="generateComptableFile">
                Télécharger le fichier comptable
            </button>
            <a href="{{ route('admin.bills.export_order_prlv', ['dateStart' => Carbon\Carbon::createFromFormat('d/m/Y', $dateStart)->format('Y-m-d'), 'dateEnd' =>  Carbon\Carbon::createFromFormat('d/m/Y', $dateEnd)->format('Y-m-d')])}}" class="btn btn-sm btn-info" target="_blank">
                Télécharger ordre prélevement
            </a>
        </div>
    </div>
</div>

