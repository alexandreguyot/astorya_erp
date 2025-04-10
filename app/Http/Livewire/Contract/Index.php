<?php

namespace App\Http\Livewire\Contract;

use App\Http\Livewire\WithSorting;
use App\Models\Contract;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Bill;

class Index extends Component
{
    use WithPagination, WithSorting, LivewireAlert;

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
        $groupedContracts = $this->getGroupedContracts();

        return view('livewire.contract.index', [
            'groupedContracts' => $groupedContracts
        ]);
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
            ->get();

        return $contracts->groupBy([
            function ($contract) {
                $contract->billing_period = $contract->calculateBillingPeriod($this->dateStart);
                return $contract->company->name;
            },
            function ($contract) {
                return $contract->billing_period;
            }
        ])->sortKeys();
    }


    public function generateAllBills()
    {
        $groupedContracts = $this->getGroupedContracts();

        foreach ($groupedContracts as $companyName => $periods) {
            foreach ($periods as $billingPeriod => $contracts) {
                $contractIds = $contracts->pluck('id')->toArray();
                $this->generateBill($companyName, implode('-', $contractIds), $billingPeriod);
            }
        }

        $this->alert('success', 'Toutes les factures ont été générées avec succès !', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'showConfirmButton' => false,
        ]);
    }


    public function generateBill($companyName, $contractIds, $date) {
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

        $no_bill = Bill::getBillNumber();
        foreach ($contracts as $contract) {
            $bill = new Bill();
            $bill->company_id = $contracts->first()->company_id;
            $bill->no_bill = $no_bill;
            $bill->generated_at = Carbon::now()->format(config('project.date_format'));
            $bill->started_at = $started_at;
            $bill->billed_at = $billed_at;
            $bill->amount = str_replace(',', '.', $contract->total_price);
            $bill->amount_vat_included = str_replace(',', '.', $contract->total_price_with_vat);
            $bill->type_period_id = $contract->type_period_id;
            $bill->contract_id = $contract->id;
            if ($bill->save()) {
                $contract->billed_at = Carbon::now()->format(config('project.date_format'));
                $contract->save();
            }
        }
        $this->alert('success', 'Facture pour '. $companyName . 'générée avec succès !', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'showConfirmButton' => false,
        ]);
    }
}
