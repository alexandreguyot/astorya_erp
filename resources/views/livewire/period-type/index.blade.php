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
                            {{ trans('cruds.periodType.fields.title') }}
                            @include('components.table.sort', ['field' => 'title'])
                        </th>
                        <th>
                            {{ trans('cruds.periodType.fields.nb_month') }}
                            @include('components.table.sort', ['field' => 'nb_month'])
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($periodTypes as $periodType)
                        <tr>
                            <td>
                                {{ $periodType->title }}
                            </td>
                            <td>
                                {{ $periodType->nb_month }}
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    {{-- @can('period_type_show')
                                        <a class="btn btn-sm btn-info mr-2" href="{{ route('admin.period-types.show', $periodType) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan --}}
                                    @can('period_type_edit')
                                        <a class="btn btn-sm btn-success mr-2" href="{{ route('admin.period-types.edit', $periodType) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('period_type_delete')
                                        <button class="btn btn-sm btn-rose mr-2" type="button" wire:click="confirm('delete', {{ $periodType->id }})" wire:loading.attr="disabled">
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
            {{ $periodTypes->links() }}
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
