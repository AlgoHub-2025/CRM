<?php

namespace App\Actions\Proposals;

use App\Models\Proposal;
use App\Models\Contract;
use App\Models\Opportunity;
use App\Actions\Opportunities\MarkOpportunityWonAction;
use App\Events\AuditableAction;
use Illuminate\Support\Facades\DB;

class AcceptProposalAction
{
    /**
     * Mark a proposal as accepted, draft a contract, and optionally convert the opportunity.
     */
    public function execute(Proposal $proposal): Proposal
    {
        if ($proposal->status === 'accepted') {
            return $proposal; // Already accepted
        }

        return DB::transaction(function () use ($proposal) {
            $oldValues = $proposal->only(['status']);
            // 1. Mark as accepted
            $proposal->update(['status' => 'accepted']);

            // 2. If it is tied to an Opportunity, unconditionally mark it won
            if ($proposal->opportunity_id) {
                $opportunity = Opportunity::find($proposal->opportunity_id);
                if ($opportunity && !$opportunity->is_won) {
                    $action = app(MarkOpportunityWonAction::class);
                    // Pass the first "won" stage id, or rely on action logic
                    $wonStage = \App\Models\PipelineStage::where('type', 'opportunity')
                                ->where('is_won', true)
                                ->first();
                                
                    if ($wonStage) {
                        $client = $action->execute($opportunity, $wonStage->id);
                        
                        // Backfill the client_id on the proposal
                        $proposal->update(['client_id' => $client->id]);
                    }
                }
            }

            // 3. Draft a Contract
            // We need a client_id to draft a contract. If one wasn't backfilled, and didn't exist, abort.
            if (!$proposal->client_id) {
                abort(500, 'Cannot generate a contract without a linked client.');
            }

            Contract::create([
                'contract_number' => 'CON-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
                'client_id' => $proposal->client_id,
                'proposal_id' => $proposal->id,
                'start_date' => now(),
                'value' => $proposal->total,
                'payment_terms' => $proposal->payment_terms,
                'status' => 'draft',
            ]);

            // Dispatch AuditableAction
            AuditableAction::dispatch(
                $proposal,
                'accepted',
                'Proposals',
                $oldValues,
                $proposal->only(['status'])
            );

            return $proposal;
        });
    }
}
