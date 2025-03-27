<?php

namespace App\Http\Livewire\PeriodType;

use App\Models\PeriodType;
use Livewire\Component;

class Edit extends Component
{
    public PeriodType $periodType;

    public function mount(PeriodType $periodType)
    {
        $this->periodType = $periodType;
    }

    public function render()
    {
        return view('livewire.period-type.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->periodType->save();

        return redirect()->route('admin.period-types.index');
    }

    protected function rules(): array
    {
        return [
            'periodType.title' => [
                'string',
                'required',
            ],
            'periodType.nb_month' => [
                'integer',
                'min:-2147483648',
                'max:2147483647',
                'required',
            ],
        ];
    }
}
