<?php

namespace App\Http\Livewire\Company;

use Livewire\Component;
use App\Models\Company;
use App\Models\Contact;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Contacts extends Component
{
    use LivewireAlert;

    public Company $company;
    public $contact;

    public function mount(Company $company)
    {
        $this->company = $company;
        $this->contact = Contact::where('id', $this->company->contact_id)->first();
        if (!$this->contact) {
            $this->contact = new Contact();
            $this->contact->company_id = $company->id;
        }
    }

    public function render()
    {
        return view('livewire.contact.edit');
    }

    public function submit()
    {
        $this->validate();

        $this->contact->save();

        $this->alert('success', 'Contact enregistré avec succès', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'text' => 'Les informations de contact ont été mises à jour.',
        ]);
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
