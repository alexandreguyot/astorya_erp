<?php

namespace App\Http\Livewire\TypeProduct;

use App\Models\TypeProduct;
use Livewire\Component;

class Edit extends Component
{
    public TypeProduct $typeProduct;

    public function mount(TypeProduct $typeProduct)
    {
        $this->typeProduct = $typeProduct;
    }

    public function render()
    {
        return view('livewire.type-product.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->typeProduct->save();

        return redirect()->route('admin.type-product.index');
    }

    protected function rules(): array
    {
        return [
            'typeProduct.code' => [
                'string',
                'required',
            ],
            'typeProduct.short_description' => [
                'string',
                'nullable',
            ],
            'typeProduct.description_longue' => [
                'string',
                'nullable',
            ],
            'typeProduct.accounting' => [
                'string',
                'nullable',
            ],
        ];
    }
}
