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
use Illuminate\Support\Str;

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
        $periodStart = Carbon::createFromFormat(config('project.date_format'), $this->dateStart)->startOfMonth();
        $periodEnd   = $periodStart->copy()->endOfMonth();

        $contracts = Contract::with([
                'type_period',
                'company',
                'contract_product_detail' => function ($q) use ($periodStart) {
                    $q->where('monthly_unit_price_without_taxe', '>', 0)
                    ->where(function ($qq) use ($periodStart) {
                        $qq->whereNull('billing_terminated_at')
                            ->orWhereDate('billing_terminated_at', '0001-01-01')
                            ->orWhereDate('billing_terminated_at', '>=', $periodStart);
                    })
                    ->with('type_product.type_contract');
                },
            ])
            ->whereNotNull('setup_at')
            ->where(function ($q) use ($periodStart) {
                $q->whereNull('terminated_at')
                ->orWhereDate('terminated_at', '>=', $periodStart);
            })
            ->when($this->search, function ($q) {
                $q->whereHas('company', function ($qq) {
                    $qq->where('companies.name', 'like', '%' . $this->search . '%');
                });
            })
            // ne pas reprendre un contrat déjà facturé ce mois
            ->where(function ($q) use ($periodStart) {
                $q->whereDoesntHave('bills', function ($qb) use ($periodStart) {
                    $qb->whereYear('generated_at',  $periodStart->year)
                    ->whereMonth('generated_at', $periodStart->month);
                });
            })
            ->get()

            // au moins un détail chargé
            ->filter(fn ($contract) => $contract->contract_product_detail->isNotEmpty())

            // garder si mois d’échéance OU si le contrat se termine dans la période (pour prorata final)
            ->filter(function ($contract) use ($periodStart, $periodEnd) {
                $setup      = Carbon::createFromFormat(config('project.date_format'), $contract->setup_at)->startOfMonth();
                $monthsDiff = $setup->diffInMonths($periodStart);
                $nbMonth    = max(1, (int) ($contract->type_period->nb_month ?? 1));
                $isOnCycle  = $periodStart->gte($setup) && ($monthsDiff % $nbMonth === 0);

                $isTerminationInPeriod = false;
                if ($contract->terminated_at) {
                    $term = Carbon::createFromFormat(config('project.date_format'), $contract->terminated_at);
                    $isTerminationInPeriod = $term->betweenIncluded($periodStart, $periodEnd);
                }

                return $isOnCycle || $isTerminationInPeriod;
            })

            // ne garde que les contrats ayant AU MOINS un détail listable et une période valide
            ->filter(function ($contract) use ($periodStart, $periodEnd) {

                // 🚫 Supprimer les contrats où la date de début = date de fin
                if ($contract->terminated_at && $contract->setup_at) {
                    try {
                        $start = Carbon::createFromFormat(config('project.date_format'), $contract->setup_at);
                        $end   = Carbon::createFromFormat(config('project.date_format'), $contract->terminated_at);
                        if ($end->equalTo($start)) {
                            return false;
                        }
                    } catch (\Exception $e) {
                        // Ignorer les erreurs de format
                    }
                }

                return $contract->contract_product_detail->contains(function ($detail) use ($periodStart, $periodEnd) {
                    if (!method_exists($detail, 'shouldListForPeriod')) return true;

                    // 🚫 Exclure les articles avec une date de fin passée
                    if (!empty($detail->billing_terminated_at)) {
                        try {
                            $endBilling = Carbon::createFromFormat(config('project.date_format'), $detail->billing_terminated_at);
                            if ($endBilling->isPast() && !$endBilling->equalTo(Carbon::createFromFormat(config('project.date_format'), '01/01/0001'))) {
                                return false;
                            }
                        } catch (\Exception $e) {
                            // ignorer erreurs
                        }
                    }

                    // 🚫 Exclure les articles dont la date de début = date de fin
                    if (!empty($detail->billing_started_at) && !empty($detail->billing_terminated_at)) {
                        try {
                            $startBilling = Carbon::createFromFormat(config('project.date_format'), $detail->billing_started_at);
                            $endBilling   = Carbon::createFromFormat(config('project.date_format'), $detail->billing_terminated_at);
                            if ($endBilling->equalTo($startBilling)) {
                                return false;
                            }
                        } catch (\Exception $e) {
                            // sécurité
                        }
                    }

                    // garder seulement si facturable
                    if (! $detail->shouldListForPeriod($periodStart, $periodEnd)) {
                        return false;
                    }

                    // 🚫 déjà facturé jusqu’à la fin de période
                    $lastRaw = $detail->getRawOriginal('last_billed_at');
                    if ($lastRaw) {
                        $last = Carbon::parse($lastRaw)->endOfDay();
                        if ($last->equalTo($periodEnd->copy()->endOfDay())) {
                            return false;
                        }
                    }

                    // 🚫 période inversée (début > fin)
                    $period = $detail->contract->calculateBillingPeriod($periodStart->format(config('project.date_format')));
                    if ($period) {
                        [$startStr, $endStr] = explode(' au ', $period);
                        $start = Carbon::createFromFormat(config('project.date_format'), $startStr);
                        $end   = Carbon::createFromFormat(config('project.date_format'), $endStr);
                        if ($end->lt($start)) {
                            return false;
                        }
                    }

                    return true;
                });
            })

            // libellé de période
            ->each(function ($contract) {
                $contract->billing_period = $contract->calculateBillingPeriod($this->dateStart);
            })

            // groupement & tri
            ->groupBy([
                fn ($contract) => optional($contract->company)->name ?? 'Sans société',
                fn ($contract) => $contract->billing_period,
            ])
            ->sortKeys();

        return $contracts;
    }

    function interpolate(string $sql, array $bindings): string
    {
        // Laravel 8+ : Str::replaceArray
        return Str::replaceArray('?', array_map(
            fn($b) => is_numeric($b) ? $b : "'".addslashes($b)."'",
            $bindings
        ), $sql);
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

        $dateStart = Carbon::createFromFormat(config('project.date_format'), $started_at)->startOfMonth()->format('Y-m-d');

        $contracts = Contract::with([
            'type_period',
            'company.city',
            'company.bank_account',
            'contract_product_detail' => function ($q) use ($dateStart) {
                $q->whereNull('billing_terminated_at')
                    ->orWhereDate('billing_terminated_at', '0001-01-01')
                    ->orWhereDate('billing_terminated_at', '>=', $dateStart);
            },
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
