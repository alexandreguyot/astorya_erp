<?php

namespace App\Http\Livewire\Contract;

use App\Http\Livewire\WithSorting;
use App\Jobs\GenerationAllBills;
use App\Jobs\GenerationSelectedBills;
use App\Jobs\ProcessBills;
use App\Models\Contract;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Bill;
use Illuminate\Pagination\LengthAwarePaginator;

class Index extends Component
{
    use WithPagination, WithSorting, LivewireAlert;

    public int $perPage;

    public array $orderable;
    public array $filterable;

    public string $search = '';

    public array $selected = [];
    public array $selectedContracts = [];

    public array $paginationOptions;

    public ?string $dateStartView = null; // Date de début
    public ?string $dateStart = null; // Date de début
    public ?string $dateEnd = null;   // Date de fin
    public ?string $dateEndView = null;   // Date de fin

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
        $this->dateStartView = Carbon::now()->startOfMonth()->format('m/Y');
        $this->dateStart = Carbon::now()->startOfMonth()->format('d/m/Y');
        $this->dateEndView = Carbon::now()->endOfMonth()->format('m/Y');
        $this->dateEnd = Carbon::now()->endOfMonth()->format('d/m/Y');
    }

    public function render()
    {
        $contracts = $this->getGroupedContracts();
        $currentPage = $this->page;
        $perPage = $this->perPage;
        $pagedData = $contracts->forPage($currentPage, $perPage);

        $groupedContracts =  new LengthAwarePaginator(
            $pagedData,
            $contracts->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.contract.index', [
            'groupedContracts' => $groupedContracts
        ]);
    }

    public function updatedDateStartView($value)
    {
        $this->dateStart = Carbon::createFromFormat('m/Y', $value)->startOfMonth()->format('d/m/Y');
        $this->dateEndView = $value;
    }
    public function updatedDateEndView($value)
    {
        $this->dateEnd = Carbon::createFromFormat('m/Y', $value)->endOfMonth()->format('d/m/Y');
    }

    private function getGroupedContracts()
    {
        $contracts = Contract::with(['type_period', 'company', 'contract_product_detail.type_product.type_contract'])
            ->whereNotNull('setup_at')
            ->where(function ($query) {
                $query->whereNull('terminated_at')
                    ->orWhere('terminated_at', '>=', Carbon::createFromFormat('d/m/Y', $this->dateStart)->startOfMonth());
            })
            ->when($this->search, function ($query) {
                $query->whereHas('company', function ($q) {
                    $q->where('companies.name', 'like', '%' . $this->search . '%');
                });
            })
            ->whereHas('type_period', function ($query) {
                $query->whereRaw('(TIMESTAMPDIFF(MONTH, terminated_at, ?) % nb_month) = 0', [
                    Carbon::createFromFormat('d/m/Y', $this->dateStart)->startOfMonth()
                ]);
            })
            ->whereDoesntHave('company.bills', function ($query) {
                $query->whereMonth('bills.generated_at', Carbon::createFromFormat('d/m/Y', $this->dateStart)->month)
                    ->whereYear('bills.generated_at', Carbon::createFromFormat('d/m/Y', $this->dateStart)->year);
            })
            ->get()
            ->filter(function ($contract) {
                return $contract->contract_product_detail->contains(function ($detail) {
                    return floatval($detail->monthly_unit_price_without_taxe) > 0;
                });
            })->groupBy([
                function ($contract) {
                    $contract->billing_period = $contract->calculateBillingPeriod($this->dateStart);
                    return $contract->company->name;
                },
                function ($contract) {
                    return $contract->billing_period;
                }
            ])->sortKeys();

        return $contracts;
    }

    public function generateSelectedBills()
    {
        dispatch(new GenerationSelectedBills($this->selectedContracts, auth()->id()));
        $this->alert('success', 'Factures en cours de génération pour celles sélectionnés.', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'showConfirmButton' => false,
        ]);
        $this->selectedContracts = [];
    }

    public function generateAllBills()
    {
        $groupedContracts = $this->getGroupedContracts();

        dispatch(new GenerationAllBills($groupedContracts, auth()->id()));

        $this->alert('success', 'Toutes les factures sont en cours de génération !', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'showConfirmButton' => false,
        ]);
    }


    public function generateBill($companyName, $contractIds, $date)
    {
        $contractIds = explode('-', $contractIds);
        $started_at = substr($date, 0, 10);
        $billed_at = substr($date, 14, 14);

        $contracts = Contract::with([
            'type_period',
            'company.city',
            'contract_product_detail.type_product.type_contract',
            'contract_product_detail.type_product.type_vat',
        ])
        ->whereIn('id', $contractIds)
        ->get();

        if ($contracts->isEmpty()) {
            $this->alert('error', 'Aucun contrat trouvé pour la facturation.', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'showConfirmButton' => false,
            ]);
            return;
        }

        dispatch(new ProcessBills(
            $companyName,
            $contractIds,
            $started_at,
            $billed_at,
            auth()->user()->id
        ));

        $this->alert('success', "Facture pour {$companyName} générée avec succès !", [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'showConfirmButton' => false,
        ]);
    }
}
