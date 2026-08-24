<?php

namespace App\Actions\Opportunities;

use App\Models\Opportunity;
use App\Models\PipelineStage;
use App\Models\Client;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use App\Events\AuditableAction;
use Illuminate\Database\UniqueConstraintViolationException;

class MarkOpportunityWonAction
{
    public function execute(Opportunity $opportunity, string $targetStageId): Client
    {
        $targetStage = PipelineStage::findOrFail($targetStageId);

        if (!$targetStage->is_won) {
            abort(400, 'Target stage is not a winning stage.');
        }

        if (!$opportunity->lead_id || !$opportunity->lead->company_id) {
            abort(400, 'Opportunity must be linked to a lead with a company before it can be converted to a client.');
        }
        
        $companyId = $opportunity->lead->company_id;

        return DB::transaction(function () use ($opportunity, $targetStage, $companyId) {
            $oldValues = $opportunity->only(['stage_id']);
            $clientAction = 'none';

            // 1. Transition the opportunity stage
            $opportunity->update(['stage_id' => $targetStage->id]);

            // 2. Check for soft-deleted client to restore
            $client = Client::withTrashed()->where('company_id', $companyId)->first();

            if ($client && $client->trashed()) {
                $client->restore();
                $clientAction = 'restored';
            }

            // 3. If no client exists at all, try to create one
            if (!$client) {
                try {
                    $client = Client::create([
                        'company_id' => $companyId,
                        'status' => 'active',
                        'converted_from_opportunity_id' => $opportunity->id,
                    ]);
                    $clientAction = 'created';
                } catch (UniqueConstraintViolationException $e) {
                    $client = Client::where('company_id', $companyId)->firstOrFail();
                    $clientAction = 'existing';
                }
            }

            // 4. Link the Opportunity to the Client
            $opportunity->update(['client_id' => $client->id]);

            // 5. Auto-populate primary_contact_id if null
            if (!$client->primary_contact_id) {
                $decisionMaker = Contact::where('company_id', $companyId)
                    ->where('is_decision_maker', true)
                    ->first();

                if ($decisionMaker) {
                    $client->update(['primary_contact_id' => $decisionMaker->id]);
                }
            }

            AuditableAction::dispatch(
                $opportunity,
                'marked_won',
                'Opportunities',
                $oldValues,
                array_merge($opportunity->only(['stage_id', 'client_id']), ['client_action' => $clientAction])
            );

            return $client;
        });
    }
}
