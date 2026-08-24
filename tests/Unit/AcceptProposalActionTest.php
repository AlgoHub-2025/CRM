<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Actions\Proposals\AcceptProposalAction;
use App\Models\Proposal;
use App\Models\Client;
use App\Models\Company;
use App\Models\Lead;
use App\Models\Opportunity;
use App\Models\PipelineStage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AcceptProposalActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepts_client_proposal_and_drafts_contract()
    {
        $client = Client::factory()->create();
        $proposal = Proposal::factory()->create([
            'client_id' => $client->id,
            'opportunity_id' => null,
            'status' => 'sent',
            'total' => 5000,
            'payment_terms' => 'Net 30'
        ]);

        $action = new AcceptProposalAction();
        $action->execute($proposal);

        $this->assertEquals('accepted', $proposal->fresh()->status);
        
        $this->assertDatabaseHas('contracts', [
            'client_id' => $client->id,
            'proposal_id' => $proposal->id,
            'value' => 5000,
            'payment_terms' => 'Net 30',
            'status' => 'draft'
        ]);
    }

    public function test_accepts_opportunity_proposal_converts_opportunity_and_drafts_contract()
    {
        // Setup the required won stage for the MarkOpportunityWonAction
        PipelineStage::factory()->create(['type' => 'opportunity', 'is_won' => true]);

        $company = Company::factory()->create();
        $lead = Lead::factory()->create(['company_id' => $company->id]);
        $opportunity = Opportunity::factory()->create([
            'lead_id' => $lead->id,
            'stage_id' => PipelineStage::factory()->create(['type' => 'opportunity', 'is_won' => false])->id
        ]);

        $proposal = Proposal::factory()->create([
            'client_id' => null,
            'opportunity_id' => $opportunity->id,
            'status' => 'sent',
            'total' => 10000
        ]);

        $action = new AcceptProposalAction();
        $action->execute($proposal);

        $proposal->refresh();
        $opportunity->refresh();

        $this->assertEquals('accepted', $proposal->status);
        $this->assertNotNull($proposal->client_id);
        $this->assertTrue((bool)$opportunity->stage->is_won);
        
        $this->assertDatabaseHas('contracts', [
            'client_id' => $proposal->client_id,
            'proposal_id' => $proposal->id,
            'value' => 10000,
        ]);
    }
}
