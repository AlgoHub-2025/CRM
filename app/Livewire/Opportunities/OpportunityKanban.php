<?php

namespace App\Livewire\Opportunities;

use Livewire\Component;
use App\Models\PipelineStage;
use App\Models\Opportunity;
use App\Actions\Opportunities\UpdateOpportunityStageAction;
use App\Actions\Opportunities\CalculatePipelineForecastAction;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OpportunityKanban extends Component
{
    use AuthorizesRequests;

    public function updateOpportunityStage(
        string $opportunityId, 
        string $newStageId, 
        array $orderedIds,
        UpdateOpportunityStageAction $updateAction
    ) {
        $opportunity = Opportunity::findOrFail($opportunityId);

        // Security Check: Ensure user is allowed to update this specific opportunity
        $this->authorize('update', $opportunity);

        $updateAction->execute($opportunity, $newStageId, $orderedIds);
        
        // No need to manually refresh stages; render() will fetch the latest state
    }

    public function render(CalculatePipelineForecastAction $calculateForecast)
    {
        $user = auth()->user();
        
        $stages = PipelineStage::where('type', 'opportunity')
            ->orderBy('order')
            ->get();
            
        $opportunities = Opportunity::query()
            ->visibleTo($user, 'view')
            ->with(['client', 'assignedTo'])
            ->orderBy('order')
            ->get();

        $stageData = $stages->map(function ($stage) use ($opportunities, $calculateForecast) {
            return [
                'stage' => $stage,
                'opportunities' => $opportunities->where('stage_id', $stage->id),
                'total_forecast' => $calculateForecast->execute($stage->id),
            ];
        });

        return view('livewire.opportunities.opportunity-kanban', [
            'stageData' => $stageData
        ]);
    }
}
