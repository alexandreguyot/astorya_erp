<?php

namespace App\Http\Livewire\Contract;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Contract;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class AnnualIndex extends Component
{
    use WithPagination;

    public int     $perPage         = 10;
    public string  $search          = '';
    public ?string $dateStart       = null;  // dd/mm/YYYY
    public ?string $dateEnd         = null;  // dd/mm/YYYY
    public string  $dateStartMonth  = '';    // YYYY-MM
    protected      $paginationTheme = 'tailwind';

    protected $queryString = [
        'search'           => ['except' => ''],
        'dateStart'        => ['except' => null],
        'dateEnd'          => ['except' => null],
        'dateStartMonth'   => ['except' => ''],
        'perPage'          => ['except' => 10],
        'page'             => ['except' => 1],
    ];

    public function mount()
    {
        $this->dateStartMonth = Carbon::now()->addMonth()->format('Y-m');
        $this->updatedDateStartMonth($this->dateStartMonth);
    }

    public function updatedDateStartMonth(string $value)
    {
        [$y, $m] = explode('-', $value);
        $this->dateStart = Carbon::create($y, $m)->startOfMonth()->format('d/m/Y');
        $this->dateEnd   = Carbon::create($y, $m)->endOfMonth()->format('d/m/Y');
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // bornes
        $start = Carbon::createFromFormat('d/m/Y', $this->dateStart)->startOfDay();
        $end   = Carbon::createFromFormat('d/m/Y', $this->dateEnd)->endOfDay();

        // requête de base
        $contracts = Contract::with([
                'company',
                'type_period',
                'contract_product_detail.type_product.type_contract'
            ])
            // **Seulement annuels**
            ->whereHas('type_period', fn($q) => $q->where('nb_month', 12))
            // actifs sur la période
            ->where(function($q) use($start){
                $q->whereNull('terminated_at')
                  ->orWhereDate('terminated_at','>=',$start);
            })
            // pas déjà facturés ce mois
            ->whereDoesntHave('bills', fn($q) =>
                $q->whereBetween('generated_at', [
                    $start->toDateTimeString(),
                    $end->toDateTimeString()
                ])
            )
            // recherche société
            ->when($this->search, fn($q) =>
                $q->whereHas('company', fn($q2)=>
                    $q2->where('name','like',"%{$this->search}%")
                )
            )
            ->orderBy('company_id')
            ->get()
            // calcul du billing_period
            ->each(fn($c) => $c->billing_period = $start->format('d/m/Y').' au '.$end->format('d/m/Y'))
            // groupBy société puis période
            ->groupBy([
                fn($c) => $c->company->name,
                fn($c) => $c->billing_period
            ]);

        // pagination manuelle
        $page   = $this->page;
        $slice  = $contracts->slice(($page-1)*$this->perPage, $this->perPage, true);
        $paged  = new LengthAwarePaginator(
            $slice,
            $contracts->count(),
            $this->perPage,
            $page,
            ['path'=>request()->url(),'query'=>request()->query()]
        );

        return view('livewire.contract.annual-index', [
            'groupedContracts' => $paged,
        ]);
    }

    public function isProcessingRow($groupKey)
    {
        return Cache::has("processing.{$groupKey}");
    }
}
