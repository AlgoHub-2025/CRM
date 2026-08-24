<?php

namespace App\Actions\Companies;

use App\Models\Company;
use App\Events\AuditableAction;

class CreateCompanyAction
{
    /**
     * Create a new company.
     *
     * @param array $data
     * @return Company
     */
    public function execute(array $data): Company
    {
        $company = Company::create($data);
        
        event(new AuditableAction(
            model: $company,
            action: 'created',
            module: 'companies',
            newValues: $company->toArray()
        ));
        
        return $company;
    }
}
