<?php

namespace App\Http\Livewire\Company;

use App\Models\City;
use App\Models\Company;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
class Edit extends Component
{
    use LivewireAlert;

    public Company $company;

    public $city = '';
    public $code_postal = '';


    public array $listsForFields = [];

    public function mount(Company $company)
    {
        $this->company = $company;
        // Préremplir les champs à partir de la ville liée
        if ($this->company->city) {
            $this->city = $this->company->city->name ?? '';
            $this->code_postal = $this->company->city->zip_code ?? '';
        }
        $this->initListsForFields();
    }

    public function render()
    {
        return view('livewire.company.edit');
    }

    public function submit()
    {
        $this->validate();

       $city = City::firstOrCreate(
            [
                'name' => trim($this->city),
                'zip_code' => trim($this->code_postal),
            ]
        );

        $this->company->city_id = $city->id;

        $this->company->save();

        $this->alert('success', 'Client mis à jour avec succès');
    }

    protected function rules(): array
    {
        return [
            'company.name' => [
                'string',
                'nullable',
            ],
            'company.address' => [
                'string',
                'nullable',
            ],
            'company.address_compl' => [
                'string',
                'nullable',
            ],
            'company.email' => [
                'email:rfc',
                'nullable',
            ],
            'company.accounting' => [
                'string',
                'nullable',
            ],
            'company.ciel_reference' => [
                'string',
                'nullable',
            ],
            'company.send_bill_type' => [
                'boolean',
            ],
            'company.one_bill_per_period' => [
                'boolean',
            ],
            'company.bill_payment_method' => [
                'boolean',
            ],
            'company.observations' => [
                'string',
                'nullable',
            ],
            'city' => ['required', 'string', 'max:255'],
            'code_postal' => ['required', 'string', 'max:10'],
        ];
    }

    protected function initListsForFields(): void
    {
        // $this->listsForFields['city'] = City::pluck('name', 'id')->toArray();
    }
}
