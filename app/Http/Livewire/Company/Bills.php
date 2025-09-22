<?php

namespace App\Http\Livewire\Company;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use App\Models\Bill;
use App\Models\Company;

class Bills extends Component
{
    use WithPagination;

    public int $companyId;
    public int $perPage = 25;

    public function mount(Company $company)
    {
        $this->companyId = $company->id;
    }

    public function render()
    {
        // On ne récupère que ce qui sert à l’affichage minimal
        $sub = Bill::selectRaw('MAX(id) as id')
            ->where('company_id', $this->companyId)
            ->groupBy('no_bill');

        $bills = Bill::whereIn('id', $sub)
            ->orderByDesc('generated_at')
            ->paginate($this->perPage, ['id','no_bill','generated_at','file_path','created_at']);


        return view('livewire.company.bills', compact('bills'));
    }
}
