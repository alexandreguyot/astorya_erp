<div>
    <div class="card-controls sm:flex">
        <div class="w-full sm:w-1/2 ">
            Recherche:
            <input type="text" wire:model.debounce.300ms="search" class="inline-block w-full sm:w-1/2 form-control" />
        </div>
        <div class="w-full sm:w-1/2 sm:text-right">
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
                            {{ trans('cruds.typeContract.fields.title') }}
                            @include('components.table.sort', ['field' => 'title'])
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($typeContracts as $typeContract)
                        <tr>
                            <td>
                                {{ $typeContract->title }}
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    @can('type_contract_edit')
                                        <a class="btn btn-sm btn-success mr-2" href="{{ route('admin.type-contract.edit', $typeContract) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('type_contract_delete')
                                        <button class="btn btn-sm btn-rose mr-2" type="button" wire:click="confirm('delete', {{ $typeContract->id }})" wire:loading.attr="disabled">
                                            {{ trans('global.delete') }}
                                        </button>
                                    @endcan
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
            @if($this->selectedCount)
                <p class="text-sm leading-5">
                    <span class="font-medium">
                        {{ $this->selectedCount }}
                    </span>
                    {{ __('Entries selected') }}
                </p>
            @endif
            {{ $typeContracts->links() }}
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
