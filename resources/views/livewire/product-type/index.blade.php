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
                        <th class="w-9">
                        </th>
                        <th class="w-28">
                            {{ trans('cruds.productType.fields.id') }}
                            @include('components.table.sort', ['field' => 'id'])
                        </th>
                        <th>
                            {{ trans('cruds.productType.fields.code') }}
                            @include('components.table.sort', ['field' => 'code'])
                        </th>
                        <th>
                            {{ trans('cruds.productType.fields.short_description') }}
                            @include('components.table.sort', ['field' => 'short_description'])
                        </th>
                        <th>
                            {{ trans('cruds.productType.fields.description_longue') }}
                            @include('components.table.sort', ['field' => 'description_longue'])
                        </th>
                        <th>
                            {{ trans('cruds.productType.fields.accounting') }}
                            @include('components.table.sort', ['field' => 'accounting'])
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productTypes as $productType)
                        <tr>
                            <td>
                                <input type="checkbox" value="{{ $productType->id }}" wire:model="selected">
                            </td>
                            <td>
                                {{ $productType->id }}
                            </td>
                            <td>
                                {{ $productType->code }}
                            </td>
                            <td>
                                {{ $productType->short_description }}
                            </td>
                            <td>
                                {{ $productType->description_longue }}
                            </td>
                            <td>
                                {{ $productType->accounting }}
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    @can('product_type_show')
                                        <a class="btn btn-sm btn-info mr-2" href="{{ route('admin.product-types.show', $productType) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan
                                    @can('product_type_edit')
                                        <a class="btn btn-sm btn-success mr-2" href="{{ route('admin.product-types.edit', $productType) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('product_type_delete')
                                        <button class="btn btn-sm btn-rose mr-2" type="button" wire:click="confirm('delete', {{ $productType->id }})" wire:loading.attr="disabled">
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
            {{ $productTypes->links() }}
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
