<?php

namespace App\Actions\Proposals;

use App\Models\Proposal;

class UpdateProposalStatusAction
{
    /**
     * Update the status of a proposal.
     */
    public function execute(Proposal $proposal, string $status): Proposal
    {
        $validStatuses = ['draft', 'sent', 'viewed', 'negotiation', 'accepted', 'rejected', 'expired'];
        
        if (!in_array($status, $validStatuses)) {
            abort(400, 'Invalid proposal status.');
        }

        if ($status === 'accepted') {
            // Defer to AcceptProposalAction for the complex logic
            return app(AcceptProposalAction::class)->execute($proposal);
        }

        $proposal->update(['status' => $status]);

        return $proposal;
    }
}
