<?php

namespace App\Actions\Contacts;

use App\Models\Contact;
use App\Events\AuditableAction;

class UpdateContactAction
{
    public function execute(Contact $contact, array $data): Contact
    {
        $oldValues = $contact->toArray();

        $contact->update([
            'name' => $data['name'] ?? $contact->name,
            'email' => array_key_exists('email', $data) ? $data['email'] : $contact->email,
            'phone' => array_key_exists('phone', $data) ? $data['phone'] : $contact->phone,
            'whatsapp' => array_key_exists('whatsapp', $data) ? $data['whatsapp'] : $contact->whatsapp,
            'designation' => array_key_exists('designation', $data) ? $data['designation'] : $contact->designation,
            'is_decision_maker' => $data['is_decision_maker'] ?? $contact->is_decision_maker,
            'notes' => array_key_exists('notes', $data) ? $data['notes'] : $contact->notes,
        ]);

        $changes = $contact->getChanges();

        if (!empty($changes)) {
            event(new AuditableAction(
                $contact,
                'updated',
                'contacts',
                array_intersect_key($oldValues, $changes),
                $changes
            ));
        }

        return $contact->fresh();
    }
}
