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
                            {{ trans('cruds.vatType.fields.id') }}
                            @include('components.table.sort', ['field' => 'id'])
                        </th>
                        <th>
                            {{ trans('cruds.vatType.fields.code') }}
                            @include('components.table.sort', ['field' => 'code'])
                        </th>
                        <th>
                            {{ trans('cruds.vatType.fields.percent') }}
                            @include('components.table.sort', ['field' => 'percent'])
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vatTypes as $vatType)
                        <tr>
                            <td>
                                <input type="checkbox" value="{{ $vatType->id }}" wire:model="selected">
                            </td>
                            <td>
                                {{ $vatType->id }}
                            </td>
                            <td>
                                {{ $vatType->code }}
                            </td>
                            <td>
                                {{ $vatType->percent }}
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    @can('vat_type_show')
                                        <a class="btn btn-sm btn-info mr-2" href="{{ route('admin.vat-types.show', $vatType) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan
                                    @can('vat_type_edit')
                                        <a class="btn btn-sm btn-success mr-2" href="{{ route('admin.vat-types.edit', $vatType) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('vat_type_delete')
                                        <button class="btn btn-sm btn-rose mr-2" type="button" wire:click="confirm('delete', {{ $vatType->id }})" wire:loading.attr="disabled">
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
            {{ $vatTypes->links() }}
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
