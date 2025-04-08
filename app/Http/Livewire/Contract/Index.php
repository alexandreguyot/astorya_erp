<?php

namespace App\Http\Livewire\Contract;

use App\Http\Livewire\WithConfirmation;
use App\Http\Livewire\WithSorting;
use App\Models\Contract;
use App\Models\Owner;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $this->sortBy            = 'company.name';
        $this->sortDirection     = 'asc';
        $this->perPage           = 100;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable         = (new Contract())->orderable;
        $this->filterable         = (new Contract())->filterable;
        $this->dateStart = Carbon::now()->startOfMonth()->format('d/m/Y');
        $this->dateEnd = Carbon::now()->endOfMonth()->format('d/m/Y');
    }

    public function render()
    {
        // Récupération des contrats sans pagination
        $contracts = Contract::with(['type_period', 'company', 'contract_product_detail.type_product.type_contract'])
            ->whereNotNull('setup_at')
            ->where(function ($query) {
                $query->whereNull('terminated_at')
                        ->orWhere('terminated_at', '>=', Carbon::createFromFormat('d/m/Y', $this->dateStart)->startOfMonth());
            })
            ->when($this->search, function ($query) {
                $query->whereHas('company', function ($q) {
                    $q->where('companies.name', 'like', '%'.$this->search.'%');
                });
            })
            ->whereHas('type_period', function ($query) {
                $query->whereRaw('(TIMESTAMPDIFF(MONTH, terminated_at, ?) % nb_month) = 0', [
                    Carbon::createFromFormat('d/m/Y', $this->dateStart)->startOfMonth()
                ]);
            })
            ->get(); // Récupère tous les contrats

        // Groupement des contrats par entreprise et période de facturation
        $groupedContracts = $contracts->groupBy([
            function ($contract) {
                $contract->billing_period = $contract->calculateBillingPeriod($this->dateStart); // Calcul de la période de facturation
                return $contract->company->name; // Groupe par entreprise
            },
            function ($contract) {
                return $contract->billing_period; // Groupe par période de facturation
            }
        ])->sortKeys();
        return view('livewire.contract.index', [
            'groupedContracts' => $groupedContracts // Passer les groupes à la vue
        ]);
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('contract_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        Contract::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }
}
