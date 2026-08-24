<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Lead;
use App\Models\Company;
use App\Models\Opportunity;

class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard', [
            'totalLeads' => Lead::count(),
            'newLeads' => Lead::whereHas('status', fn($q) => $q->where('name', 'New'))->count(),
            'totalCompanies' => Company::count(),
            'totalOpportunities' => Opportunity::count(),
            'pipelineValue' => Opportunity::sum('value') / 100,
            'recentLeads' => Lead::with(['company', 'status'])->latest()->take(5)->get(),
        ]);
    }
}
