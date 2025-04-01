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
        $this->sortBy            = 'company.name';
        $this->sortDirection     = 'asc';
        $this->perPage           = 10;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable         = (new Contract())->orderable;
        $this->filterable         = (new Contract())->filterable;
        $this->dateStart = Carbon::now()->startOfMonth()->format('d/m/Y');
        $this->dateEnd = Carbon::now()->endOfMonth()->format('d/m/Y');
    }

    public function render()
    {
        $query = Contract::with(['type_period', 'company', 'contract_product_detail.type_product.type_contract'])
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
            $query->whereRaw('(TIMESTAMPDIFF(MONTH, terminated_at, ?) % nb_month) = 0', [Carbon::createFromFormat('d/m/Y', $this->dateStart)->startOfMonth()]);
        });

        $contracts = $query->paginate($this->perPage);

        $contracts->getCollection()->transform(function ($contract) {
            $contract->billing_period = $this->getBillingPeriodAttribute($contract, $this->dateStart);
            $contract->total_price = $contract->total_price;
            return $contract;
        });


        return view('livewire.contract.index', compact('contracts', 'query'));
    }

    public function getBillingPeriodAttribute($contrat, $dateStart = null)
    {
        if (!$contrat->type_period || !$contrat->type_period->nb_month) {
            return null;
        }

        $nbMonth = $contrat->type_period->nb_month;
        $day = Carbon::createFromFormat('d/m/Y', $contrat->setup_at)->format('d');
        $startBilling = Carbon::createFromFormat('d/m/Y', $dateStart)->day($day);
        $endBilling = $startBilling->copy()->addMonths($nbMonth)->subDay(1);

        return $startBilling->format('d/m/Y') . ' au ' . $endBilling->format('d/m/Y');
    }

    public function pdf()
    {
        //TODO
    }

    public function deleteSelected()
    {
        abort_if(Gate::denies('contract_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        Contract::whereIn('id', $this->selected)->delete();

        $this->resetSelected();
    }
}
