<?php

namespace App\Http\Livewire\TypeContract;

use App\Models\TypeContract;
use Livewire\Component;

class Create extends Component
{
    public TypeContract $typeContract;

    public function mount(TypeContract $typeContract)
    {
        $this->typeContract = $typeContract;
    }

    public function render()
    {
        return view('livewire.contract-type.create');
    }

    public function submit()
    {
        $this->validate();

        $this->typeContract->save();

        return redirect()->route('admin.contract-types.index');
    }

    protected function rules(): array
    {
        return [
            'typeContract.title' => [
                'string',
                'required',
            ],
        ];
    }
}
