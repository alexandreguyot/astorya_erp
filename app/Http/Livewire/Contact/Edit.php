<?php

namespace App\Http\Livewire\Contact;

use App\Models\Contact;
use Livewire\Component;

class Edit extends Component
{
    public Contact $contact;

    public function mount(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function render()
    {
        return view('livewire.contact.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->contact->save();

        return redirect()->route('admin.contacts.index');
    }

    protected function rules(): array
    {
        return [
            'contact.lastname' => [
                'string',
                'nullable',
            ],
            'contact.firstname' => [
                'string',
                'nullable',
            ],
            'contact.title' => [
                'string',
                'nullable',
            ],
            'contact.email' => [
                'email:rfc',
                'nullable',
            ],
            'contact.is_director' => [
                'boolean',
            ],
        ];
    }
}
