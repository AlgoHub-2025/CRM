<?php

namespace App\Livewire\Proposals;

use App\Models\Proposal;
use Livewire\Component;
use Livewire\WithPagination;

class ProposalList extends Component
{
    use WithPagination;

    public function render()
    {
        $proposals = Proposal::with(['client.company', 'opportunity.lead.company'])
            ->latest()
            ->paginate(10);
            
        return view('livewire.proposals.proposal-list', [
            'proposals' => $proposals,
        ]);
    }
}
