<?php

namespace App\Http\Livewire\TypeVat;

use App\Models\TypeVat;
use Livewire\Component;

class Create extends Component
{
    public TypeVat $typeVat;

    public function mount(TypeVat $typeVat)
    {
        $this->typeVat = $typeVat;
    }

    public function render()
    {
        return view('livewire.type-vat.create');
    }

    public function submit()
    {
        $this->validate();

        $this->typeVat->save();

        return redirect()->route('admin.type-vat.index');
    }

    protected function rules(): array
    {
        return [
            'typeVat.code' => [
                'string',
                'required',
            ],
            'typeVat.percent' => [
                'numeric',
                'nullable',
            ],
        ];
    }
}
