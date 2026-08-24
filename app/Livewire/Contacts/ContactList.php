<?php

namespace App\Livewire\Contacts;

use Livewire\Component;
use App\Models\Company;
use App\Models\Contact;
use Livewire\WithPagination;

class ContactList extends Component
{
    use WithPagination;

    public Company $company;
    public $search = '';
    public $decisionMakerFilter = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDecisionMakerFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = $this->company->contacts();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%')
                  ->orWhere('email', 'ilike', '%' . $this->search . '%')
                  ->orWhere('designation', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->decisionMakerFilter) {
            $query->where('is_decision_maker', true);
        }

        $contacts = $query->latest()->paginate(10);

        return view('livewire.contacts.contact-list', [
            'contacts' => $contacts
        ]);
    }
}
