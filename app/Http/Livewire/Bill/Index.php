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

    public function updatedDateStartMonth(string $value)
    {
        [$year, $month] = explode('-', $value);
        $this->dateStart = Carbon::create($year, $month)->startOfMonth()->format('d/m/Y');
        $this->dateEndMonth = $value;
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
        $query = Bill::with(['company', 'type_period'])
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
            return [
                'no_bill' => $noBill,
                'company' => $group->first()->company->name,
                'generated_at' => $group->first()->generated_at,
                'sent_at' => $group->first()->sent_at,
                'bills' => $group,
                'total_ht' => $group->sum('amount'),
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

    public function downloadZipFile()
    {
        $monthFolder = Carbon::createFromFormat('d/m/Y', $this->dateStart)
            ->startOfMonth()
            ->format('m-Y');
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
}
