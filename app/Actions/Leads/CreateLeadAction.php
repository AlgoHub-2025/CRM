<?php

namespace App\Actions\Leads;

use App\Models\Lead;
use App\Models\Company;
use App\Events\AuditableAction;
use Illuminate\Support\Facades\DB;

class CreateLeadAction
{
    /**
     * Create a new lead (and optionally a company inline).
     *
     * @param array $data
     * @return Lead
     */
    public function execute(array $data): Lead
    {
        return DB::transaction(function () use ($data) {
            // Inline company creation
            if (empty($data['company_id']) && !empty($data['company_name'])) {
                $company = Company::create(['name' => $data['company_name']]);
                
                event(new AuditableAction(
                    model: $company,
                    action: 'created',
                    module: 'companies',
                    newValues: $company->toArray()
                ));
                
                $data['company_id'] = $company->id;
            }
            
            // Unset company_name since it's not a column on leads table
            unset($data['company_name']);

            $lead = Lead::create($data);
            
            event(new AuditableAction(
                model: $lead,
                action: 'created',
                module: 'leads',
                newValues: $lead->toArray()
            ));
            
            return $lead;
        });
    }
}
