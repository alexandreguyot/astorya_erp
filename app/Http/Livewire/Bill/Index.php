<?php

namespace App\Http\Livewire\Bill;

use App\Http\Livewire\WithConfirmation;
use App\Http\Livewire\WithSorting;
use App\Models\Bill;
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
        $this->sortBy            = 'no_bill';
        $this->sortDirection     = 'asc';
        $this->perPage           = 100;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable         = (new Bill())->orderable;
        $this->filterable         = (new Bill())->filterable;
        $this->dateStart = Carbon::now()->startOfMonth()->format('d/m/Y'); // 1er jour du mois
        $this->dateEnd = Carbon::now()->endOfMonth()->format('d/m/Y'); // Dernier jour du mois
    }

    public function render()
    {
        $query = Bill::with(['company', 'typePeriod'])
        ->whereNotNull('no_bill')
        // ->where('no_bill', 'like', 'FACT-%')
        ->when($this->dateStart && !$this->dateEnd, function ($query) {
            $dateStart = $this->convertDateFormat($this->dateStart, 'start');
            $query->where('generated_at', '>=', $dateStart);
        })
        ->when(!$this->dateStart && $this->dateEnd, function ($query) {
            $dateEnd = $this->convertDateFormat($this->dateEnd, 'end');
            $query->where('generated_at', '<=', $dateEnd);
        })
        ->when($this->dateStart && $this->dateEnd, function ($query) {
            $dateStart = $this->convertDateFormat($this->dateStart, 'start');
            $dateEnd = $this->convertDateFormat($this->dateEnd, 'end');
            $query->whereBetween('generated_at', [$dateStart, $dateEnd]);
        })
        ->when($this->search, function ($query) {
            $query->whereHas('company', function($q) {
                $q->where('companies.name', 'like', '%'.$this->search.'%');
            });
        })->orderBy($this->sortBy, $this->sortDirection);

        $bills = $query->paginate($this->perPage);

        return view('livewire.bill.index', compact('bills', 'query'));
    }

    private function convertDateFormat($date, $type)
    {
       // Vérifier le format fourni
        if (preg_match('/^\d{4}$/', $date)) {
            // Format YYYY -> Année complète
            return $type === 'start' ? "$date-01-01 00:00:00" : "$date-12-31 23:59:59";
        }

        if (preg_match('/^\d{2}\/\d{4}$/', $date)) {
            // Format MM/YYYY -> Mois complet
            [$month, $year] = explode('/', $date);

            // Trouver le dernier jour du mois
            $lastDay = date('t', strtotime("$year-$month-01"));

            return $type === 'start' ? "$year-$month-01 00:00:00" : "$year-$month-$lastDay 23:59:59";
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            // Format DD/MM/YYYY -> Un seul jour
            [$day, $month, $year] = explode('/', $date);
            return $type === 'start' ? "$year-$month-$day 00:00:00" : "$year-$month-$day 23:59:59";
        }

        return null;
    }

    public function deleteSelected()
    {
        Bill::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }

    public function delete(Bill $bill)
    {
        $bill->delete();
    }

    public function confirmDeleteSelected()
    {
        if (Gate::allows('bill_delete')) {
            $this->confirm('Êtes-vous sûr de vouloir supprimer ces factures ?', [
                'accept' => 'deleteSelected',
            ]);
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }
    public function confirmDelete(Bill $bill)
    {
        if (Gate::allows('bill_delete')) {
            $this->confirm('Êtes-vous sûr de vouloir supprimer cette facture ?', [
                'accept' => 'delete',
                'params' => [$bill],
            ]);
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }
    public function confirmDeleteAll()
    {
        if (Gate::allows('bill_delete')) {
            $this->confirm('Êtes-vous sûr de vouloir supprimer toutes les factures ?', [
                'accept' => 'deleteAll',
            ]);
        } else {
            abort(Response::HTTP_FORBIDDEN);
        }
    }

    public function getPdf()
    {
        $this->confirm('Êtes-vous sûr de vouloir télécharger le PDF ?', [
            'accept' => 'getPdf',
        ]);
    }
}
