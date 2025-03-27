<?php

namespace App\Http\Livewire\Owner;

use App\Models\Owner;
use Livewire\Component;

class Create extends Component
{
    public Owner $owner;

    public function mount(Owner $owner)
    {
        $this->owner = $owner;
    }

    public function render()
    {
        return view('livewire.owner.create');
    }

    public function submit()
    {
        $this->validate();

        $this->owner->save();

        return redirect()->route('admin.owners.index');
    }

    protected function rules(): array
    {
        return [
            'owner.name' => [
                'string',
                'required',
            ],
            'owner.address' => [
                'string',
                'required',
            ],
            'owner.zip_code' => [
                'string',
                'max:5',
                'required',
            ],
            'owner.city' => [
                'string',
                'required',
            ],
            'owner.email' => [
                'string',
                'required',
            ],
            'owner.phone' => [
                'string',
                'required',
            ],
            'owner.web_site_address' => [
                'string',
                'required',
            ],
            'owner.siret' => [
                'string',
                'required',
            ],
            'owner.capital' => [
                'string',
                'required',
            ],
            'owner.bic' => [
                'string',
                'required',
            ],
            'owner.iban' => [
                'string',
                'required',
            ],
            'owner.hotline_name' => [
                'string',
                'required',
            ],
            'owner.hotline_phone' => [
                'string',
                'required',
            ],
            'owner.hotline_email' => [
                'string',
                'required',
            ],
            'owner.accounting_manager' => [
                'string',
                'required',
            ],
            'owner.accounting_phone' => [
                'string',
                'required',
            ],
            'owner.accounting_email' => [
                'string',
                'required',
            ],
        ];
    }
}
