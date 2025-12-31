<?php

namespace App\Http\Livewire\Bill;

use App\Http\Livewire\WithSorting;
use App\Models\Bill;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ComptableExport;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Admin\BillController;
use App\Jobs\GenerateBillPdf;
use Illuminate\Support\Facades\DB;


class Index extends Component
{
    use WithPagination, WithSorting, LivewireAlert;

    public int $perPage;

    public array $orderable;
    public array $filterable;

    public string $search = '';

    public array $selected = [];
    public array $selectedBills = [];
    public array $sending = [];

    public array $paginationOptions;

    public ?string $dateStart = null;
    public ?string $dateEnd   = null;
    public ?string $dateStartMonth = null;
    public ?string $dateEndMonth   = null;

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

    public function decrementBothMonths()
    {
        // Parse les deux mois actuels
        $start = Carbon::createFromFormat('Y-m', $this->dateStartMonth)->subMonth();
        $end   = Carbon::createFromFormat('Y-m', $this->dateEndMonth)->subMonth();

        // Recalcule dateStart / dateEnd en d/m/Y et stocke les YYYY-MM
        $this->dateStartMonth = $start->format('Y-m');
        $this->dateStart      = $start->startOfMonth()->format('d/m/Y');

        $this->dateEndMonth   = $end->format('Y-m');
        $this->dateEnd        = $end->endOfMonth()->format('d/m/Y');
    }

    /**
     * Décale à la fois dateStartMonth et dateEndMonth d’un mois en avant.
     */
    public function incrementBothMonths()
    {
        $start = Carbon::createFromFormat('Y-m', $this->dateStartMonth)->addMonth();
        $end   = Carbon::createFromFormat('Y-m', $this->dateEndMonth)->addMonth();

        $this->dateStartMonth = $start->format('Y-m');
        $this->dateStart      = $start->startOfMonth()->format('d/m/Y');

        $this->dateEndMonth   = $end->format('Y-m');
        $this->dateEnd        = $end->endOfMonth()->format('d/m/Y');
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
        $this->perPage           = 10;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable         = (new Bill())->orderable;
        $this->filterable         = (new Bill())->filterable;
        $this->dateStart = Carbon::now()->startOfMonth()->format('d/m/Y');
        $this->dateEnd = Carbon::now()->endOfMonth()->format('d/m/Y');
        $this->dateStartMonth = Carbon::now()->startOfMonth()->format('Y-m');
        $this->dateEndMonth   = Carbon::now()->endOfMonth()->format('Y-m');
        $this->updatedDateStartMonth($this->dateStartMonth);
        $this->updatedDateEndMonth($this->dateEndMonth);
    }

    public function render() {
        $query = Bill::with(['company', 'type_period', 'contract.contract_product_detail.type_product.type_contract',
            'contract.contract_product_detail.type_product.type_vat'])
            ->whereNotNull('no_bill')
            ->where('no_bill', 'like', 'FACT-%')

            ->when($this->dateStart && $this->dateEnd, function ($query) {
                $dateStart = $this->convertDateFormat($this->dateStart, 'start');
                $dateEnd = $this->convertDateFormat($this->dateEnd, 'end');
                $query->whereBetween('generated_at', [$dateStart, $dateEnd]);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('company', function($q) {
                    $q->where('companies.name', 'like', '%'.$this->search.'%');
                })
                ->orWhere('no_bill', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        $bills = $query->get();

        $groupedBills = $bills->groupBy('no_bill');

        $billGroups = $groupedBills->map(function ($group, $noBill) {
            $contract = $group->first()->contract;
            return [
                'no_bill' => $noBill,
                'company' => $group->first()->company->name ?? 'Sans société',
                'company_id' => $group->first()->company->id ?? '999999999999',
                'generated_at' => $group->first()->generated_at,
                'sent_at' => $group->first()->sent_at,
                'bills' => $group,
                'total_ht' => $group->sum('amount'),
                'contract' => $contract,
                'details'  => $contract->contract_product_detail,
            ];
        });

        $currentPage = $this->page;
        $perPage = $this->perPage;
        $pagedData = $billGroups->forPage($currentPage, $perPage);

        $paginatedGroups = new LengthAwarePaginator(
            $pagedData,
            $billGroups->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );


        return view('livewire.bill.index', [
            'billGroups' => $paginatedGroups,
        ]);
    }

    public function exportPcaYear()
    {
        // On déduit l'année à partir de la date affichée dans la vue
        $year = Carbon::createFromFormat('d/m/Y', $this->dateStart)->year;

        $service = app(\App\Services\PcaService::class);
        $lines = $service->getPcaLinesForYear($year);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PcaExport($lines),
            "PCA-$year.xlsx"
        );
    }


    private function convertDateFormat($date, $type)
    {
        if (preg_match('/^\d{4}$/', $date)) {
            return $type === 'start' ? "$date-01-01 00:00:00" : "$date-12-31 23:59:59";
        }

        if (preg_match('/^\d{2}\/\d{4}$/', $date)) {
            [$month, $year] = explode('/', $date);

            $lastDay = date('t', strtotime("$year-$month-01"));

            return $type === 'start' ? "$year-$month-01 00:00:00" : "$year-$month-$lastDay 23:59:59";
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            [$day, $month, $year] = explode('/', $date);
            return $type === 'start' ? "$year-$month-$day 00:00:00" : "$year-$month-$day 23:59:59";
        }

        return null;
    }

    public function regenerateBill(string $noBill): void
    {
        if (Cache::has("regenerating.bill.{$noBill}")) {
            $this->alert('info', "Régénération déjà en cours.");
            return;
        }

        Cache::add("regenerating.bill.{$noBill}", true, now()->addMinutes(10));

        $bill = Bill::where('no_bill', $noBill)->first();

        if (! $bill) {
            $this->alert('error', "Facture {$noBill} introuvable.");
            return;
        }

        // 🔒 Sécurité admin
        if (! auth()->check() || auth()->id() !== 1) {
            $this->alert('error', "Action non autorisée.");
            return;
        }

        // 🔒 BLOQUAGE : déjà envoyée
        if ($bill->sent_at) {
            $this->alert(
                'warning',
                "Cette facture a déjà été envoyée le {$bill->sent_at}. Régénération bloquée."
            );
            return;
        }

        $dateStarted = $bill->started_at instanceof Carbon
            ? $bill->started_at
            : Carbon::createFromFormat('d/m/Y', $bill->started_at)->startOfDay();

        dispatch(new \App\Jobs\GenerateBillPdf(
            $noBill,
            $dateStarted
        ));

        $this->alert('success', "Regénération lancée pour la facture {$noBill}.");
    }


    public function getLastJobError(string $noBill): ?string
    {
        $job = DB::table('failed_jobs')
            ->where('payload', 'like', '%"no_bill":"' . $noBill . '"%')
            ->orderByDesc('failed_at')
            ->first();

        if (! $job) {
            return null;
        }

        return str($job->exception)
            ->limit(300)
            ->toString();
    }


    public function downloadZipFile()
    {
        $monthFolder = Carbon::createFromFormat('d/m/Y', $this->dateStart)
            ->startOfMonth()
            ->format('Y-m');
        $pdfPath  = "private/factures/{$monthFolder}";
        $fullPath = storage_path("app/{$pdfPath}");

        $query = Bill::with(['company', 'type_period'])
            ->whereNotNull('no_bill')
            ->where('no_bill', 'like', 'FACT-%')
            ->when($this->dateStart && $this->dateEnd, function($q) {
                $dateStart = $this->convertDateFormat($this->dateStart, 'start');
                $dateEnd   = $this->convertDateFormat($this->dateEnd,   'end');
                $q->whereBetween('generated_at', [$dateStart, $dateEnd]);
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        $bills = $query->get();

        if ($bills->isEmpty()) {
            $this->alert('error', "Aucune facture trouvée avec ces critères.");
            return;
        }

        $existingFiles = collect(glob("{$fullPath}/*.pdf"))
            ->map(fn($path) => basename($path))
            ->all();
        $controller = app(BillController::class);
        foreach ($bills as $bill) {
            $filename = $bill->no_bill . '.pdf';
            if (! in_array($filename, $existingFiles, true)) {
                $controller->pdf($bill->no_bill, $this->dateStart);
            }
        }

        $zipFileName = "factures_{$monthFolder}.zip";
        Storage::makeDirectory('tmp');
        $zipFilePath = storage_path("app/tmp/{$zipFileName}");

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->alert('error', "Impossible de créer le fichier ZIP.");
            return;
        }

        foreach (glob("{$fullPath}/*.pdf") as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();

        return response()->download($zipFilePath)
                         ->deleteFileAfterSend(true);
    }

    public function generateComptableFile()
    {
        $dateStart = $this->dateStart;
        $dateEnd   = $this->dateEnd;

        $fileName  = "export-comptable.xlsx";

        return Excel::download(
            new ComptableExport($dateStart, $dateEnd),
            $fileName
        );
    }

    public function isSending(string $noBill): bool
    {
        return Cache::has("sending.bill.{$noBill}");
    }

    public function sendInvoice(string $noBill): void
    {
        $bill = Bill::with('company', 'company.contact')->where('no_bill', $noBill)->first();

        if (! $bill) {
            $this->alert('error', "Facture {$noBill} introuvable.");
            return;
        }
        Cache::put("sending.bill.{$noBill}", true, now()->addHour());
        dispatch(new \App\Jobs\SendBillEmail($bill));
    }

     public function sendErrorInvoice(string $noBill): void
    {
        $bill = Bill::with('company', 'company.contact')->where('no_bill', $noBill)->first();

        if (! $bill) {
            $this->alert('error', "Facture {$noBill} introuvable.");
            return;
        }

        dispatch(new \App\Jobs\ErrorSentEmail($bill));
    }

    public function sendMail(string $noBill)
    {
        $this->sendInvoice($noBill);
    }

    public function sendSelectedBills()
    {
        if (empty($this->selectedBills)) {
            $this->alert('error', "Aucune facture sélectionnée.");
            return;
        }

        $toSend = Bill::whereIn('no_bill', $this->selectedBills)
                    ->whereNull('sent_at')
                    ->groupBy('no_bill')
                    ->pluck('no_bill')
                    ->toArray();

        if (empty($toSend)) {
            $this->alert('info', "Toutes les factures sélectionnées ont déjà été envoyées.");
        } else {
            foreach ($toSend as $noBill) {
                $this->sendInvoice($noBill);
            }
            $this->alert('success', "Envoi lancé pour " . count($toSend) . " factures.");
        }

        $this->selectedBills = [];
    }

    public function sendAllBills()
    {
        $query = Bill::with(['company', 'type_period'])
            ->whereNotNull('no_bill')
            ->where('no_bill', 'like', 'FACT-%')
            ->when($this->dateStart && $this->dateEnd, function($q) {
                $dateStart = $this->convertDateFormat($this->dateStart, 'start');
                $dateEnd   = $this->convertDateFormat($this->dateEnd,   'end');
                $q->whereBetween('generated_at', [$dateStart, $dateEnd]);
            })
            ->when($this->search, function ($q) {
                $q->whereHas('company', fn($q2) =>
                        $q2->where('companies.name', 'like', '%'.$this->search.'%')
                    )
                ->orWhere('no_bill', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        $bills = $query->get();
        $noBillsOnPage = $bills->pluck('no_bill')->unique()->toArray();

        $toSend = Bill::query()
            ->select('no_bill')
            ->whereIn('no_bill', $noBillsOnPage)
            ->whereNull('sent_at')
            ->distinct()
            ->pluck('no_bill')
            ->toArray();

        if (empty($toSend)) {
            $this->alert('info', "Toutes les factures ont déjà été envoyées.");
            return;
        }

        foreach ($toSend as $noBill) {
            $this->sendInvoice($noBill);
        }

        $this->alert('success', "Envoi lancé pour " . count($toSend) . " facture(s) sur cette page.");
    }

    public function sendAllErrorMail()
    {
        $query = Bill::with(['company', 'type_period'])
            ->whereNotNull('no_bill')
            ->where('no_bill', 'like', 'FACT-%')
            ->when($this->dateStart && $this->dateEnd, function($q) {
                $dateStart = $this->convertDateFormat($this->dateStart, 'start');
                $dateEnd   = $this->convertDateFormat($this->dateEnd,   'end');
                $q->whereBetween('generated_at', [$dateStart, $dateEnd]);
            })
            ->when($this->search, function ($q) {
                $q->whereHas('company', fn($q2) =>
                        $q2->where('companies.name', 'like', '%'.$this->search.'%')
                    )
                ->orWhere('no_bill', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        $bills = $query->get();
        $noBillsOnPage = $bills->pluck('no_bill')->unique()->toArray();

        $toSend = Bill::query()
            ->select('no_bill')
            ->whereIn('no_bill', $noBillsOnPage)
            ->distinct()
            ->pluck('no_bill')
            ->toArray();

        if (empty($toSend)) {
            $this->alert('info', "Toutes les factures ont déjà été envoyées.");
            return;
        }

        foreach ($toSend as $noBill) {
            $this->sendErrorInvoice($noBill);
        }

        $this->alert('success', "Envoi lancé pour " . count($toSend) . " facture(s) sur cette page.");
    }
}
