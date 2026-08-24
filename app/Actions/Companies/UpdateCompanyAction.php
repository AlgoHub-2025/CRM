<?php

namespace App\Actions\Companies;

use App\Models\Company;
use App\Events\AuditableAction;

class UpdateCompanyAction
{
    /**
     * Update an existing company.
     *
     * @param Company $company
     * @param array $data
     * @return Company
     */
    public function execute(Company $company, array $data): Company
    {
        $oldValues = $company->getOriginal();
        
        $company->update($data);
        
        event(new AuditableAction(
            model: $company,
            action: 'updated',
            module: 'companies',
            oldValues: array_intersect_key($oldValues, $company->getChanges()),
            newValues: $company->getChanges()
        ));
        
        return $company;
    }
}
