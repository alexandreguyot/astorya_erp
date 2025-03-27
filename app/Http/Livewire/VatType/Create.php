<?php

namespace App\Http\Livewire\VatType;

use App\Models\VatType;
use Livewire\Component;

class Create extends Component
{
    public VatType $vatType;

    public function mount(VatType $vatType)
    {
        $this->vatType = $vatType;
    }

    public function render()
    {
        return view('livewire.vat-type.create');
    }

    public function submit()
    {
        $this->validate();

        $this->vatType->save();

        return redirect()->route('admin.vat-types.index');
    }

    protected function rules(): array
    {
        return [
            'vatType.code' => [
                'string',
                'required',
            ],
            'vatType.percent' => [
                'numeric',
                'nullable',
            ],
        ];
    }
}
