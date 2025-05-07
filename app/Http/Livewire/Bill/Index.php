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
    public array $allBils = [];

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
        $this->sortBy            = 'no_bill';
        $this->sortDirection     = 'asc';
        $this->perPage           = 10;
        $this->paginationOptions = config('project.pagination.options');
        $this->orderable         = (new Bill())->orderable;
        $this->filterable         = (new Bill())->filterable;
        $this->dateStartView = Carbon::now()->startOfMonth()->format('m/Y');
        $this->dateStart = Carbon::now()->startOfMonth()->format('d/m/Y');
        $this->dateEndView = Carbon::now()->endOfMonth()->format('m/Y');
        $this->dateEnd = Carbon::now()->endOfMonth()->format('d/m/Y');
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

        $this->allBils = $bills->groupBy('no_bill')->toArray();

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

    public function updatedDateStartView($value)
    {
        $this->dateStart = Carbon::createFromFormat('m/Y', $value)->startOfMonth()->format('d/m/Y');
        $this->dateEndView = $value;
    }
    public function updatedDateEndView($value)
    {
        $this->dateEnd = Carbon::createFromFormat('m/Y', $value)->endOfMonth()->format('d/m/Y');
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
        $monthFolder = Carbon::createFromFormat('m/Y', $this->dateStartView)
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
        $toSend = Bill::whereIn('no_bill', $this->allBils)
                    ->whereNull('sent_at')
                    ->pluck('no_bill')
                    ->toArray();

        if (empty($toSend)) {
            $this->alert('info', "Toutes les factures de cette page ont déjà été envoyées.");
        } else {
            foreach ($toSend as $noBill) {
                $this->sendInvoice($noBill);
            }
            $this->alert('success', "Envoi lancé pour toutes les factures non envoyées.");
        }
    }
}
