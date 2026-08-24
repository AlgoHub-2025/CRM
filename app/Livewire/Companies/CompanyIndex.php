<?php

namespace App\Livewire\Companies;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Company;

class CompanyIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $companies = Company::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('industry', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('livewire.companies.company-index', [
            'companies' => $companies
        ]);
    }
}
