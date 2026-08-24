<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\PipelineStage;
use App\Models\Client;
use App\Events\AuditableAction;
use Illuminate\Support\Facades\DB;

class ConvertLeadAction
{
    public function execute(Lead $lead): Opportunity
    {
        return DB::transaction(function () use ($lead) {
            // First, if the lead has a company but the company isn't a client yet, convert the company to a client (or we just link the client)
            $client = null;
            if ($lead->company_id) {
                $client = Client::firstOrCreate(
                    ['company_id' => $lead->company_id],
                    ['status' => 'active']
                );
            }

            // Get the first opportunity stage
            $stage = PipelineStage::where('type', 'opportunity')->orderBy('order')->first();

            // Create the Opportunity
            $opportunity = Opportunity::create([
                'lead_id' => $lead->id,
                'client_id' => $client ? $client->id : null,
                'title' => $lead->name . ' - Opportunity',
                'service' => $lead->interested_service ?? 'General Service',
                'value' => $lead->estimated_budget ?? 0,
                'probability' => 10,
                'assigned_to' => $lead->assigned_to,
                'stage_id' => $stage ? $stage->id : null,
                'notes' => $lead->description,
            ]);

            event(new AuditableAction(
                model: $opportunity,
                action: 'created',
                module: 'opportunities',
                newValues: $opportunity->toArray()
            ));

            // Mark lead as converted
            $lead->update(['converted_at' => now()]);

            return $opportunity;
        });
    }
}
