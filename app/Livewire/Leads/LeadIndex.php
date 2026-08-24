<?php

namespace App\Livewire\Leads;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Lead;

class LeadIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Policy scoping is already handled by the Controller which authorized 'viewAny'.
        // However, we must scope the query itself so users only see what they are allowed to see.
        $query = Lead::query()->visibleTo(auth()->user(), 'view');

        $leads = $query->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.leads.lead-index', [
            'leads' => $leads
        ]);
    }
}
