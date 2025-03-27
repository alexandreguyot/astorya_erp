<?php

namespace App\Http\Livewire\ProductType;

use App\Models\ProductType;
use Livewire\Component;

class Edit extends Component
{
    public ProductType $productType;

    public function mount(ProductType $productType)
    {
        $this->productType = $productType;
    }

    public function render()
    {
        return view('livewire.product-type.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->productType->save();

        return redirect()->route('admin.product-types.index');
    }

    protected function rules(): array
    {
        return [
            'productType.code' => [
                'string',
                'required',
            ],
            'productType.short_description' => [
                'string',
                'nullable',
            ],
            'productType.description_longue' => [
                'string',
                'nullable',
            ],
            'productType.accounting' => [
                'string',
                'nullable',
            ],
        ];
    }
}
