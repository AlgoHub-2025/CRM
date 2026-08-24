<?php

namespace App\Livewire\Clients;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Client;
use Illuminate\Database\Eloquent\Builder;

class ClientList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = ['search', 'sortField', 'sortDirection'];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Client::query()
            ->with(['company', 'primaryContact'])
            ->whereHas('company', function (Builder $query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });

        // Apply RBAC Scoping
        $user = auth()->user();
        if (!$user->hasPermissionTo('clients.view.all')) {
            // Can only view own clients
            // 1. Where user is the account manager of the company
            // 2. Or user owns an opportunity for the company
            // 3. Or user owns a lead for the company
            $query->whereHas('company', function ($q) use ($user) {
                $q->where('account_manager_id', $user->employee->id)
                  ->orWhereHas('opportunities', function($q2) use ($user) {
                      $q2->where('assigned_to', $user->employee->id);
                  })
                  ->orWhereHas('leads', function($q3) use ($user) {
                      $q3->where('assigned_to', $user->employee->id);
                  });
            });
        }

        // Sorting by relationship (company name) requires joining or sorting collection. 
        // For simplicity, sort by native fields or join if sorting by company name.
        if ($this->sortField === 'company.name') {
            $query->join('companies', 'clients.company_id', '=', 'companies.id')
                  ->select('clients.*')
                  ->orderBy('companies.name', $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        return view('livewire.clients.client-list', [
            'clients' => $query->paginate(15)
        ]);
    }
}
