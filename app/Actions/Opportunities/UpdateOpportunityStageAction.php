<?php

namespace App\Actions\Opportunities;

use App\Models\Opportunity;
use App\Events\AuditableAction;
use Illuminate\Support\Facades\DB;

class UpdateOpportunityStageAction
{
    /**
     * Update an opportunity's stage and optionally its order.
     *
     * @param Opportunity $opportunity
     * @param string $newStageId
     * @param int|null $newOrder
     * @return Opportunity
     */
    public function execute(Opportunity $opportunity, string $newStageId, array $orderedIds = []): Opportunity
    {
        DB::transaction(function () use ($opportunity, $newStageId, $orderedIds, &$oldValues) {
            $oldValues = $opportunity->getOriginal();
            
            $opportunity->stage_id = $newStageId;
            $opportunity->save();

            if (!empty($orderedIds)) {
                foreach ($orderedIds as $index => $id) {
                    Opportunity::where('id', $id)->update(['order' => $index]);
                }
            }
        });
        
        event(new AuditableAction(
            model: $opportunity,
            action: 'updated',
            module: 'opportunities',
            oldValues: array_intersect_key($oldValues, $opportunity->getChanges()),
            newValues: $opportunity->getChanges()
        ));
        
        return $opportunity;
    }
}
