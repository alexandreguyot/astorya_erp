<?php

namespace App\Http\Livewire\Company;

use Livewire\Component;
use App\Models\BankAccount as BankAccountModel;
use App\Models\Company;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class BankAccount extends Component
{
    use LivewireAlert;

    public $bankAccount;
    public Company $company;

    public function mount(Company $company)
    {
        $this->company = $company;
        $this->bankAccount = BankAccountModel::where('id', $this->company->bank_account_id)->first();
        if (!$this->bankAccount) {
            $this->bankAccount = new BankAccountModel();
        }
    }

    public function render()
    {
        return view('livewire.bank-account.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->bankAccount->save();
        $this->company->bank_account_id = $this->bankAccount->id;
        $this->company->save();

        $this->alert('success', 'Compte bancaire enregistré avec succès', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'text' => 'Les informations du compte bancaire ont été mises à jour.',
        ]);
    }

    protected function rules(): array
    {
        return [
            'bankAccount.no_rum' => [
                'string',
                'nullable',
            ],
            'bankAccount.effective_start_date' => [
                'nullable',
                'date_format:' . config('project.date_format'),
            ],
            'bankAccount.bic' => [
                'string',
                'nullable',
            ],
            'bankAccount.iban' => [
                'string',
                'nullable',
            ],
        ];
    }
}
