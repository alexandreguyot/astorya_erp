<?php

namespace App\Http\Livewire\TypePeriod;

use App\Models\TypePeriod;
use Livewire\Component;

class Create extends Component
{
    public TypePeriod $typePeriod;

    public function mount(TypePeriod $typePeriod)
    {
        $this->typePeriod = $typePeriod;
    }

    public function render()
    {
        return view('livewire.period-type.create');
    }

    public function submit()
    {
        $this->validate();

        $this->typePeriod->save();

        return redirect()->route('admin.period-types.index');
    }

    protected function rules(): array
    {
        return [
            'typePeriod.title' => [
                'string',
                'required',
            ],
            'typePeriod.nb_month' => [
                'integer',
                'min:-2147483648',
                'max:2147483647',
                'required',
            ],
        ];
    }
}
