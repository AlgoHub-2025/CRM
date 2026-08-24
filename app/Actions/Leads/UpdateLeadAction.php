<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use App\Events\AuditableAction;

class UpdateLeadAction
{
    /**
     * Update an existing lead.
     *
     * @param Lead $lead
     * @param array $data
     * @return Lead
     */
    public function execute(Lead $lead, array $data): Lead
    {
        $oldValues = $lead->getOriginal();
        
        $lead->update($data);
        
        event(new AuditableAction(
            model: $lead,
            action: 'updated',
            module: 'leads',
            oldValues: array_intersect_key($oldValues, $lead->getChanges()),
            newValues: $lead->getChanges()
        ));
        
        return $lead;
    }
}
