<?php

namespace App\Http\Livewire\BankAccount;

use App\Models\BankAccount;
use Livewire\Component;

class Edit extends Component
{
    public BankAccount $bankAccount;

    public function mount(BankAccount $bankAccount)
    {
        $this->bankAccount = $bankAccount;
    }

    public function render()
    {
        return view('livewire.bank-account.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->bankAccount->save();

        return redirect()->route('admin.bank-accounts.index');
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
