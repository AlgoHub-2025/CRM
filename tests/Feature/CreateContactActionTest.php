<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateContactActionTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    public function test_it_creates_contact_and_logs_audit_event()
    {
        $company = \App\Models\Company::factory()->create();
        
        \Illuminate\Support\Facades\Event::fake();

        $action = new \App\Actions\Contacts\CreateContactAction();
        
        $contactData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'is_decision_maker' => true,
        ];

        $contact = $action->execute($company, $contactData);

        $this->assertEquals('John Doe', $contact->name);
        $this->assertTrue($contact->is_decision_maker);
        $this->assertEquals($company->id, $contact->company_id);

        \Illuminate\Support\Facades\Event::assertDispatched(\App\Events\AuditableAction::class, function ($event) use ($contact) {
            return $event->action === 'created'
                && $event->module === 'contacts'
                && $event->model->id === $contact->id;
        });
    }
}
