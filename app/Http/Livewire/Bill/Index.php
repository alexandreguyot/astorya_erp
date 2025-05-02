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


class Index extends Component
{
    use WithPagination, WithSorting, LivewireAlert;

    public int $perPage;

    public array $orderable;
    public array $filterable;

    public string $search = '';

    public array $selected = [];

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
            ->when($this->dateStart && !$this->dateEnd, function ($query) {
                $dateStart = $this->convertDateFormat($this->dateStart, 'start');
                $query->where('generated_at', '>=', $dateStart);
            })
            ->when(!$this->dateStart && $this->dateEnd, function ($query) {
                $dateEnd = $this->convertDateFormat($this->dateEnd, 'end');
                $query->where('generated_at', '<=', $dateEnd);
            })
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
                'send_at' => $group->first()->generated_at,
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
       // Vérifier le format fourni
        if (preg_match('/^\d{4}$/', $date)) {
            // Format YYYY -> Année complète
            return $type === 'start' ? "$date-01-01 00:00:00" : "$date-12-31 23:59:59";
        }

        if (preg_match('/^\d{2}\/\d{4}$/', $date)) {
            // Format MM/YYYY -> Mois complet
            [$month, $year] = explode('/', $date);

            // Trouver le dernier jour du mois
            $lastDay = date('t', strtotime("$year-$month-01"));

            return $type === 'start' ? "$year-$month-01 00:00:00" : "$year-$month-$lastDay 23:59:59";
        }

        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            // Format DD/MM/YYYY -> Un seul jour
            [$day, $month, $year] = explode('/', $date);
            return $type === 'start' ? "$year-$month-$day 00:00:00" : "$year-$month-$day 23:59:59";
        }

        return null;
    }

    public function downloadZipFile()
    {
        $monthFolder = Carbon::createFromFormat('m/Y', $this->dateStartView)->startOfMonth()->format('m-Y');;

        $pdfPath = "private/factures/{$monthFolder}";
        $fullPath = storage_path("app/{$pdfPath}");

        if (!is_dir($fullPath)) {
            $this->alert('error', "Aucune facture trouvée pour le mois {$monthFolder}.");
            return null;
        }

        // 3. Créer un fichier ZIP temporaire
        $zipFileName = "factures_{$monthFolder}.zip";
        $zipFilePath = storage_path("app/tmp/{$zipFileName}");

        // Créer le dossier tmp si besoin
        Storage::makeDirectory('tmp');

        $zip = new ZipArchive();
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->alert('error','Impossible de créer le fichier ZIP.');
            return null;
        }

        // 4. Ajouter tous les fichiers PDF du dossier
        $files = glob("{$fullPath}/*.pdf");
        foreach ($files as $file) {
            $zip->addFile($file, basename($file)); // Ajout en conservant juste le nom
        }

        $zip->close();

        // 5. Télécharger le fichier ZIP et le supprimer après
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }

    public function generateComptableFile()
{
    $date = now()->format('Y-m-d_His');
    $fileName = "export-comptable-{$date}.xlsx";

    return Excel::download(new ComptableExport($this->dateStart, $this->dateEnd), $fileName);
}
}
