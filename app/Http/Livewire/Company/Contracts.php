<?php

namespace App\Http\Livewire\Company;

use Livewire\Component;
use App\Models\Company;
use Illuminate\Support\Carbon;

class Contracts extends Component
{
    public Company $company;
    public $contracts;
    public $bills;

    public array $listsForFields = [];

    public function mount(Company $company)
    {
        $this->company = $company;
    }

    public function render()
    {
        $this->contracts = $this->company->contracts()
        ->with(['bills', 'lastBill'])
        ->get()
        ->values();

        return view('livewire.company.contracts');
    }
}
