<?php

namespace App\Http\Livewire\ContractType;

use App\Models\ContractType;
use Livewire\Component;

class Edit extends Component
{
    public ContractType $contractType;

    public function mount(ContractType $contractType)
    {
        $this->contractType = $contractType;
    }

    public function render()
    {
        return view('livewire.contract-type.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->contractType->save();

        return redirect()->route('admin.contract-types.index');
    }

    protected function rules(): array
    {
        return [
            'contractType.title' => [
                'string',
                'required',
            ],
        ];
    }
}
