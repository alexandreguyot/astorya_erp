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
    public ?string $dateStart       = null;    // dd/mm/YYYY
    public ?string $dateEnd         = null;    // dd/mm/YYYY
    public string  $dateStartMonth  = '';      // YYYY-MM

    protected $paginationTheme = 'tailwind';

    public array $selectedContracts = [];

    public array $allDueContractIds;

    protected $queryString = [
        'search'          => ['except' => ''],
        'dateStartMonth'  => ['except' => ''],
        'page'            => ['except' => 1],
        'perPage'         => ['except' => 10],
    ];

    public function mount()
    {
        // Initialise sur le mois prochain
        $this->dateStartMonth = Carbon::now()->addMonth()->format('Y-m');
        $this->updatedDateStartMonth($this->dateStartMonth);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function prevMonth(): void
    {
        [$y, $m] = explode('-', $this->dateStartMonth);
        $prev    = Carbon::create($y, $m)->subMonth();

        // Met à jour le mois
        $this->dateStartMonth = $prev->format('Y-m');
        // Recalcule immédiatement les bornes
        $this->dateStart      = $prev->copy()->startOfMonth()->format('d/m/Y');
        $this->dateEnd        = $prev->copy()->endOfMonth()  ->format('d/m/Y');

        $this->resetPage();
    }

    public function nextMonth(): void
    {
        [$y, $m] = explode('-', $this->dateStartMonth);
        $next    = Carbon::create($y, $m)->addMonth();

        $this->dateStartMonth = $next->format('Y-m');
        $this->dateStart      = $next->copy()->startOfMonth()->format('d/m/Y');
        $this->dateEnd        = $next->copy()->endOfMonth()  ->format('d/m/Y');

        $this->resetPage();
    }

    public function updatedDateStartMonth(string $value)
    {
        [$y, $m] = explode('-', $value);
        $dt      = Carbon::create($y, $m);
        $this->dateStart = $dt->startOfMonth()->format('d/m/Y');
        $this->dateEnd   = $dt->endOfMonth()  ->format('d/m/Y');
        $this->resetPage();
    }

    public function render()
    {
        $start = Carbon::createFromFormat('d/m/Y', $this->dateStart)->startOfDay();
        $end   = Carbon::createFromFormat('d/m/Y', $this->dateEnd)->endOfDay();

        $year = $start->year;

        // 1) Récupère contrats annuels non facturés ce mois
        $contracts = Contract::with([
                'company',
                'contract_product_detail.type_product.type_contract',
            ])
            ->where('type_period_id', 1)
            ->where(fn($q) => $q
                ->whereNull('terminated_at')
                ->orWhereDate('terminated_at', '>=', $start)
            )
            ->whereDoesntHave('bills', fn($q) =>
                $q->whereBetween('generated_at', [$start, $end])
            )
            ->when($this->search, fn($q) =>
                $q->whereHas('company', fn($q2) =>
                    $q2->where('name', 'like', "%{$this->search}%")
                )
            )
            ->where(function($q) use ($year) {
                $q->whereNull('validated_at_for_one_year')
                ->orWhereYear('validated_at_for_one_year', '<', $year);
            })
            ->whereHas('contract_product_detail.type_product.type_contract')
            ->get()
            // Calcul du billing_period et filtrage cycle ou terminaison
            ->each(fn($c) => $c->billing_period = $c->calculateBillingPeriod($this->dateStart))
            ->filter(function($c) use($start) {
                $current = $start->copy()->startOfMonth();
                $setup   = Carbon::createFromFormat(
                              config('project.date_format'),
                              $c->setup_at
                          )->startOfMonth();
                $months  = $setup->diffInMonths($current);

                $onCycle = $current->gte($setup) && $months % 12 === 0;
                $onTerm  = $c->terminated_at
                         && Carbon::createFromFormat(config('project.date_format'), $c->terminated_at)
                                  ->startOfMonth()->eq($current);

                // On garde si cycle ou terminaison et total > 0
                return ($onCycle || $onTerm)
                    && $c->calculateTotalPrice(Carbon::createFromFormat(config('project.date_format'), $this->dateStart)) > 0;
            });
        $this->allDueContractIds = $contracts->pluck('id')->toArray();

        // 2) Pagination manuelle
        $page  = $this->page;
        $slice = $contracts->slice(($page-1)*$this->perPage, $this->perPage, true);

        $paged = new LengthAwarePaginator(
            $slice,
            $contracts->count(),
            $this->perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.contract.annual-index', [
            'dueContracts' => $paged,
        ]);
    }

   public function validateGroup(string $company, string $id, string $period)
    {
        Contract::where('id', $id)
            ->update(['validated_at_for_one_year' => now()]);

        $this->resetPage();
        session()->flash('success', "Contrat {$id} ({$company} - {$period}) validé pour un an.");
    }

    public function validateSelected(): void
    {
        if (empty($this->selectedContracts)) {
            $this->dispatchBrowserEvent('notify', [
                'type'    => 'error',
                'message' => 'Aucun contrat sélectionné.'
            ]);
            return;
        }

        Contract::whereIn('id', $this->selectedContracts)
                ->update(['validated_at_for_one_year' => now()]);

        // on revient en page 1 et vide la sélection
        $this->resetPage();
        $this->selectedContracts = [];

        session()->flash('success', count($this->selectedContracts) . ' contrat(s) validés.');
    }

    public function validateAll(): void
    {
        if (empty($this->allDueContractIds)) {
            $this->dispatchBrowserEvent('notify', [
                'type'    => 'info',
                'message' => 'Aucun contrat à valider.'
            ]);
            return;
        }

        Contract::whereIn('id', $this->allDueContractIds)
                ->update(['validated_at_for_one_year' => now()]);

        // on revient à la page 1 et on vide la sélection
        $this->resetPage();
        $this->selectedContracts = [];

        session()->flash('success', count($this->allDueContractIds) . ' contrat(s) validés.');
    }


    public function isProcessingRow($key)
    {
        return Cache::has("processing.{$key}");
    }
}
