<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Opportunity;
use App\Models\PipelineStage;
use App\Actions\Opportunities\UpdateOpportunityStageAction;
use Illuminate\Support\Facades\Event;
use App\Events\AuditableAction;

class UpdateOpportunityStageActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_updates_stage_and_fires_audit_event()
    {
        Event::fake([AuditableAction::class]);

        $stage1 = PipelineStage::create(['name' => 'New', 'order' => 1, 'type' => 'opportunity']);
        $stage2 = PipelineStage::create(['name' => 'Qualified', 'order' => 2, 'type' => 'opportunity']);

        $opportunity = Opportunity::create([
            'title' => 'My Opp',
            'stage_id' => $stage1->id,
            'value' => 5000,
            'probability' => 10
        ]);

        $action = new UpdateOpportunityStageAction();
        $action->execute($opportunity, $stage2->id, [$opportunity->id]);

        $this->assertEquals($stage2->id, $opportunity->fresh()->stage_id);

        Event::assertDispatched(AuditableAction::class, function ($event) use ($opportunity, $stage1, $stage2) {
            return $event->model->is($opportunity)
                && $event->action === 'updated'
                && $event->module === 'opportunities'
                && $event->oldValues['stage_id'] === $stage1->id
                && $event->newValues['stage_id'] === $stage2->id;
        });
    }
}
