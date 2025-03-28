<?php

namespace App\Http\Livewire\Bill;

use App\Http\Livewire\WithConfirmation;
use App\Http\Livewire\WithSorting;
use App\Models\Bill;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, WithSorting, WithConfirmation;

    public int $perPage;

    public array $orderable;

    public string $search = '';

    public array $selected = [];

    public array $paginationOptions;

    public ?string $dateStart = null; // Date de début
    public ?string $dateEnd = null;   // Date de fin

    protected $queryString = [
        'search' => [
            'except' => '',
        ],
        'dateStart' => [
            'except' => null,
        ],
        'dateEnd' => [
            'except' => null,
        ],
        'sortBy' => [
            'except' => 'id',
        ],
        'sortDirection' => [
            'except' => 'desc',
        ],

    ];

    public function getSelectedCountProperty()
    {
        return count($this->selected);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function resetSelected()
    {
        $this->selected = [];
    }

    public function mount()
    {
        $this->sortBy            = 'id';
        $this->sortDirection     = 'desc';
        $this->perPage           = 100;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable         = (new Bill())->orderable;
    }

    public function render()
    {
        $query = Bill::with(['company', 'typePeriod'])
        ->when($this->dateStart && $this->dateEnd, function ($query) {
            $query->whereBetween('generated_at', [$this->dateStart, $this->dateEnd]);
        })->advancedFilter([
            's'               => $this->search ?: null,
            'order_column'    => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $bills = $query->paginate($this->perPage);

        return view('livewire.bill.index', compact('bills', 'query'));
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('bill_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        Bill::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(Bill $bill)
    {
        abort_if(Gate::denies('bill_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $bill->delete();
    }
}
