<div>
    <div class="mb-4">
        <button wire:click="showAddModal" class="btn bg-red-400 text-white">
            Ajouter un article
        </button>
    </div>
    <div class="overflow-x-auto bg-white shadow rounded mt-5">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                <th class="px-4 py-2 text-left">Article</th>
                <th class="px-4 py-2 text-left">Réf. Article</th>
                <th class="px-4 py-2 text-center">Date de début<br/>de facturation</th>
                <th class="px-4 py-2 text-center">Date de fin<br/>de facturation</th>
                <th class="px-4 py-2 text-center">Date de dernière<br/>facturation</th>
                <th class="px-4 py-2 text-center">Quantité</th>
                <th class="px-4 py-2 text-right">Prix unitaire<br/>mensuel HT</th>
                <th class="px-4 py-2 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($existingProducts as $detail)
                <tr class="hover:bg-gray-50">
                  <td class="px-4 py-3">{{ $detail->designation }}</td>
                  <td class="px-4 py-3">{{ $detail->type_product->code }}</td>
                  <td class="px-4 py-3 text-center">
                    {{ $detail->billing_started_at }}
                  </td>
                  <td class="px-4 py-3 text-center">
                    {{ $detail->billing_terminated_at }}
                  </td>
                  <td class="px-4 py-3 text-center">
                    {{ optional($detail->last_billed_at)
                           ->format(config('project.date_format')) }}
                  </td>
                  <td class="px-4 py-3 text-center">{{ $detail->quantity }}</td>
                  <td class="px-4 py-3 text-right">
                    {{ number_format($detail->monthly_unit_price_without_taxe, 2, ',', ' ') }}
                  </td>
                  <td class="px-4 py-3 text-center">
                    <button wire:click="editDetail({{ $detail->id }})"
                            class="btn btn-sm btn-success">
                      Modifier
                    </button>
                  </td>
                </tr>
            @endforeach
                @if(count($existingProducts) === 0)
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-gray-500">
                        Aucun article n’est encore attaché à ce contrat.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @if($showEditModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl mx-4">
            <div class="px-4 py-3 border-b">
                <h2 class="text-lg font-semibold">Éditer l’article</h2>
            </div>
            <form wire:submit.prevent="updateDetail" class="p-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium">Désignation</label>
                    <input type="text"wire:model.defer="editDetailData.designation" class="form-control"/>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm">Quantité</label>
                        <input type="number" wire:model.defer="editDetailData.quantity" class="form-control"/>
                    </div>
                    <div>
                        <label class="block text-sm">P.U. Mensuel HT</label>
                        <input type="text" wire:model.defer="editDetailData.monthly_unit_price_without_taxe" class="form-control"/>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm">Début facturation</label>
                        <input type="date" wire:model.defer="editDetailData.billing_started_at" id="billing_started_at" name="billing_started_at" class="form-control"/>
                    </div>
                    <div>
                        <label class="block text-sm">Fin facturation</label>
                        <input type="date" wire:model.defer="editDetailData.billing_terminated_at" id="billing_terminated_at" name="billing_terminated_at" class="form-control"/>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" wire:click="$set('showEditModal', false)" class="px-4 py-2 bg-gray-200 rounded">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">
                        Enregistrer
                    </button>
                </div>
            </form>
            </div>
        </div>
    @endif
    @if($showAddModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl mx-4">
            <div class="px-4 py-3 border-b">
                <h2 class="text-lg font-semibold">Ajouter un article</h2>
            </div>
            <form wire:submit.prevent="saveNewProduct" class="p-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium">Produit</label>
                    <x-select-list class="form-control" required id="type_product_id" name="type_product_id" :options="$this->listsForFields['products']" wire:model="newProductData.type_product_id" />
                </div>
                <div>
                    <label class="block text-sm">Désignation</label>
                    <input type="text" wire:model.defer="newProductData.designation" class="form-control"/>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm">Quantité</label>
                        <input type="number" wire:model.defer="newProductData.quantity" class="form-control"/>
                        @error('newProductData.quantity')<span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block text-sm">P.U. Mensuel HT</label>
                        <input type="text" wire:model.defer="newProductData.monthly_unit_price_without_taxe" class="form-control"/>
                        @error('newProductData.monthly_unit_price_without_taxe')<span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm">Début facturation</label>
                        <input type="date" wire:model.defer="newProductData.billing_started_at" id="billing_started_at" name="billing_started_at" class="form-control"/>
                        @error('newProductData.billing_started_at')<span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div>
                        <label class="block text-sm">Fin facturation</label>
                        <input type="date" wire:model.defer="newProductData.billing_terminated_at" id="billing_terminated_at" name="billing_terminated_at" class="form-control"/>
                        @error('newProductData.billing_terminated_at')<span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="flex justify-end space-x-2 pt-2">
                    <button type="button" wire:click="$set('showAddModal', false)" class="btn btn-sm bg-gray-300">
                        Annuler
                    </button>
                    <button type="submit"  class="btn btn-sm btn-success">
                        Ajouter
                    </button>
                </div>
            </form>
            </div>
        </div>
    @endif
</div>
