<?php

namespace App\Http\Livewire\Contract;

use App\Http\Livewire\WithConfirmation;
use App\Http\Livewire\WithSorting;
use App\Models\Contract;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Index extends Component
{
    use WithPagination, WithSorting, WithConfirmation;

    public int $perPage;

    public array $orderable;
    public array $filterable;

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
        $this->orderable         = (new Contract())->orderable;
        $this->filterable         = (new Contract())->filterable;
        $this->dateStart = Carbon::now()->startOfMonth()->format('d/m/Y'); // 1er jour du mois
        $this->dateEnd = Carbon::now()->endOfMonth()->format('d/m/Y'); // Dernier jour du mois
    }

    public function render()
    {
        $query = Contract::with(['company'])
        ->when($this->dateStart && !$this->dateEnd, function ($query) {
            $dateStart = $this->convertDateFormat($this->dateStart, 'start');
            $query->where('started_at', '>=', $dateStart);
        })
        ->when(!$this->dateStart && $this->dateEnd, function ($query) {
            $dateEnd = $this->convertDateFormat($this->dateEnd, 'end');
            $query->where('started_at', '<=', $dateEnd);
        })
        ->when($this->dateStart && $this->dateEnd, function ($query) {
            $dateStart = $this->convertDateFormat($this->dateStart, 'start');
            $dateEnd = $this->convertDateFormat($this->dateEnd, 'end');
            $query->whereBetween('started_at', [$dateStart, $dateEnd]);
        })
        ->when($this->search, function ($query) {
            $query->whereHas('company', function($q) {
                $q->where('companies.name', 'like', '%'.$this->search.'%');
            });
        })
        ->advancedFilter([
            's'               => $this->search ?: null,
            'order_column'    => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $contracts = $query->paginate($this->perPage);

        return view('livewire.contract.index', compact('contracts', 'query'));
    }

    private function convertDateFormat($date, $type)
    {
       // Vérifier le format fourni
        if (preg_match('/^\d{4}$/', $date)) {
            // Format YYYY -> Année complète
            return $type === 'start' ? "$date-01-01" : "$date-12-31";
        }

        if (preg_match('/^\d{2}\/\d{4}$/', $date)) {
            // Format MM/YYYY -> Mois complet
            [$month, $year] = explode('/', $date);

            // Trouver le dernier jour du mois
            $lastDay = date('t', strtotime("$year-$month-01"));

            return $type === 'start' ? "$year-$month-01" : "$year-$month-$lastDay";
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            // Format DD/MM/YYYY -> Un seul jour
            [$day, $month, $year] = explode('/', $date);
            return $type === 'start' ? "$year-$month-$day" : "$year-$month-$day";
        }

        return null;
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('contract_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        Contract::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(Contract $contract)
    {
        abort_if(Gate::denies('contract_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $contract->delete();
    }
}
