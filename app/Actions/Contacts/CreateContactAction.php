<?php

namespace App\Actions\Contacts;

use App\Models\Contact;
use App\Models\Company;
use App\Events\AuditableAction;

class CreateContactAction
{
    public function execute(Company $company, array $data): Contact
    {
        $contact = $company->contacts()->create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'designation' => $data['designation'] ?? null,
            'is_decision_maker' => $data['is_decision_maker'] ?? false,
            'notes' => $data['notes'] ?? null,
        ]);

        event(new AuditableAction(
            $contact,
            'created',
            'contacts',
            null,
            $contact->toArray()
        ));

        return $contact;
    }
}
