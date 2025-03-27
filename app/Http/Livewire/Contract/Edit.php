<?php

namespace App\Http\Livewire\Contract;

use App\Models\Company;
use App\Models\Contract;
use Livewire\Component;

class Edit extends Component
{
    public Contract $contract;

    public array $listsForFields = [];

    public function mount(Contract $contract)
    {
        $this->contract = $contract;
        $this->initListsForFields();
    }

    public function render()
    {
        return view('livewire.contract.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->contract->save();

        return redirect()->route('admin.contracts.index');
    }

    protected function rules(): array
    {
        return [
            'contract.company_id' => [
                'integer',
                'exists:companies,id',
                'nullable',
            ],
            'contract.setup_at' => [
                'nullable',
                'date_format:' . config('project.datetime_format'),
            ],
            'contract.established_at' => [
                'nullable',
                'date_format:' . config('project.datetime_format'),
            ],
            'contract.started_at' => [
                'nullable',
                'date_format:' . config('project.datetime_format'),
            ],
            'contract.terminated_at' => [
                'nullable',
                'date_format:' . config('project.datetime_format'),
            ],
            'contract.billed_at' => [
                'nullable',
                'date_format:' . config('project.datetime_format'),
            ],
            'contract.validated_at' => [
                'nullable',
                'date_format:' . config('project.datetime_format'),
            ],
        ];
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['company'] = Company::pluck('name', 'id')->toArray();
    }
}
