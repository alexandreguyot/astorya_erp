<?php

namespace App\Http\Livewire\Contract;

use App\Http\Livewire\WithSorting;
use Livewire\Component;
use Livewire\WithPagination;
use App\Jobs\ProcessBills;
use App\Models\Contract;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

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

    public ?string $dateStart = null;
    public ?string $dateEnd   = null;
    public ?string $dateStartMonth = null;
    public ?string $dateEndMonth   = null;

    public array $processingRows = [];

    protected $listeners = ['refreshComponent' => '$refresh'];

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
        $this->perPage           = 10;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable         = (new Contract())->orderable;
        $this->filterable         = (new Contract())->filterable;
        $this->dateStart = Carbon::now()->startOfMonth()->format('d/m/Y');
        $this->dateEnd = Carbon::now()->endOfMonth()->format('d/m/Y');
        $this->dateStartMonth = Carbon::now()->startOfMonth()->format('Y-m');
        $this->dateEndMonth   = Carbon::now()->endOfMonth()->format('Y-m');
        $this->updatedDateStartMonth($this->dateStartMonth);
        $this->updatedDateEndMonth($this->dateEndMonth);
    }

    public function updatedDateStartMonth(string $value)
    {
        [$year, $month] = explode('-', $value);
        $this->dateStart = Carbon::create($year, $month)->startOfMonth()->format('d/m/Y');
        $this->dateEndMonth = $value;
        $this->dateEnd = Carbon::create($year, $month)->endOfMonth()->format('d/m/Y');
    }

    public function updatedDateEndMonth(string $value)
    {
        [$year, $month] = explode('-', $value);
        $this->dateEnd = Carbon::create($year, $month)->endOfMonth()->format('d/m/Y');
    }

    public function decrementBothMonths()
    {
        $start = Carbon::createFromFormat('Y-m', $this->dateStartMonth)->subMonth();
        $end   = Carbon::createFromFormat('Y-m', $this->dateEndMonth)->subMonth();

        $this->dateStartMonth = $start->format('Y-m');
        $this->dateStart      = $start->startOfMonth()->format('d/m/Y');

        $this->dateEndMonth   = $end->format('Y-m');
        $this->dateEnd        = $end->endOfMonth()->format('d/m/Y');
    }

    public function incrementBothMonths()
    {
        $start = Carbon::createFromFormat('Y-m', $this->dateStartMonth)->addMonth();
        $end   = Carbon::createFromFormat('Y-m', $this->dateEndMonth)->addMonth();

        $this->dateStartMonth = $start->format('Y-m');
        $this->dateStart      = $start->startOfMonth()->format('d/m/Y');

        $this->dateEndMonth   = $end->format('Y-m');
        $this->dateEnd        = $end->endOfMonth()->format('d/m/Y');
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

    public function isProcessingRow($groupKey)
    {
        return Cache::has("processing.{$groupKey}");
    }

    private function getGroupedContracts()
    {
        $dateStart = Carbon::createFromFormat(config('project.date_format'), $this->dateStart)->startOfMonth();
        $endOfMonth = Carbon::createFromFormat(config('project.date_format'), $this->dateStart)->endOfMonth();

        $contracts = Contract::with(['type_period', 'company', 'contract_product_detail.type_product.type_contract'])
            ->whereNotNull('setup_at')
            ->where(function ($query) use ($dateStart) {
                $query->whereNull('terminated_at')
                      ->orWhereDate('terminated_at', '>=', $dateStart);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('company', function ($q) {
                    $q->where('companies.name', 'like', '%' . $this->search . '%');
                });
            })
            ->where(function($q) use ($dateStart) {
                $q->whereDoesntHave('bills', function($q2) use ($dateStart) {
                    $q2->whereMonth('generated_at', $dateStart->month)
                       ->whereYear('generated_at',  $dateStart->year);
                })
                ->orWhere(function($q3) use ($dateStart) {
                    $q3->whereYear('terminated_at', $dateStart->year)
                       ->whereMonth('terminated_at', $dateStart->month)
                       ->whereDate('terminated_at', '>=', $dateStart->copy()->startOfMonth());
                });
            })
            ->whereHas('contract_product_detail', function ($query) use ($dateStart, $endOfMonth) {
                $query->where('monthly_unit_price_without_taxe', '>', 0)
                      ->where(function ($q) use ($dateStart, $endOfMonth) {
                          $q->whereNull('billing_terminated_at')
                            ->orWhereDate('billing_terminated_at', '0001-01-01')
                            ->orWhereDate('billing_terminated_at', '>=', $endOfMonth);
                      });
            })
            ->get()
            ->filter(function($contract) use ($dateStart) {
                $current    = $dateStart->copy()->startOfMonth();
                $setup      = Carbon::createFromFormat(config('project.date_format'), $contract->setup_at)
                                    ->startOfMonth();
                $monthsDiff = $setup->diffInMonths($current);

                // 1) C’est un mois « ordinaire » aligné sur nb_month
                $isOnCycle = $current->gte($setup) && $monthsDiff % $contract->type_period->nb_month === 0;

                // 2) OU c’est le mois de terminaison (prorata)
                $isTerminationMonth = false;
                if ($contract->terminated_at) {
                    $term = Carbon::createFromFormat(config('project.date_format'), $contract->terminated_at);
                    $isTerminationMonth = $term->year === $current->year && $term->month === $current->month;
                }

                return $isOnCycle || $isTerminationMonth;
            })
            ->each(function ($contract) {
                $contract->billing_period = $contract->calculateBillingPeriod($this->dateStart);
            })
            ->groupBy([
                fn ($contract) => $contract->company->name,
                fn ($contract) => $contract->billing_period,
            ])
            ->sortKeys();

        return $contracts;
    }

    public function generateSelectedBills()
    {
        foreach ($this->selectedContracts as $selected) {
            $data = json_decode($selected, true);
            $ids  = implode('-', $data['contracts']);
            $this->generateBill($data['company'], $ids, $data['date']);
        }

        $this->alert('success', 'Factures en cours de génération pour celles sélectionnées.', [
            'position' => 'top-end',
            'timer'    => 3000,
            'toast'    => true,
        ]);

        $this->selectedContracts = [];
    }


    public function generateAllBills()
    {
        $groupedContracts = $this->getGroupedContracts();

        foreach ($groupedContracts as $companyName => $periods) {
            foreach ($periods as $billingPeriod => $contracts) {
                $ids = implode('-', $contracts->pluck('id')->toArray());
                $this->generateBill($companyName, $ids, $billingPeriod);
            }
        }

        $this->alert('success', 'Toutes les factures sont en cours de génération...', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'showConfirmButton' => false,
        ]);
    }

    public function generateBill($companyName, $contractIds, $date)
    {
        $groupKey = md5($companyName . $date . $contractIds);

        Cache::put("processing.{$groupKey}", true);

        $contractIds = explode('-', $contractIds);
        $started_at = substr($date, 0, 10);
        $billed_at = substr($date, 14, 14);

        $contracts = Contract::with([
            'type_period',
            'company.city',
            'company.bank_account',
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
            Cache::forget("processing.{$groupKey}");
            return;
        }

        dispatch(new ProcessBills(
            $companyName,
            $contractIds,
            $started_at,
            $billed_at,
            auth()->user()->id,
            $groupKey
        ));
    }
}
