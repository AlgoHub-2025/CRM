<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Proposal;
use App\Models\Client;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Opportunity;
use App\Models\Lead;
use App\Actions\Proposals\SendProposalAction;
use App\Notifications\ProposalSentNotification;
use Illuminate\Support\Facades\Notification;

class ProposalNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_proposal_to_primary_contact()
    {
        Notification::fake();
        $company = Company::factory()->create();
        $contact = Contact::factory()->create(['company_id' => $company->id, 'email' => 'primary@test.com']);
        $client = Client::factory()->create(['company_id' => $company->id, 'primary_contact_id' => $contact->id]);
        $proposal = Proposal::factory()->create(['client_id' => $client->id, 'status' => 'draft']);

        app(SendProposalAction::class)->execute($proposal);

        Notification::assertSentOnDemand(
            ProposalSentNotification::class,
            function ($notification, $channels, $notifiable) use ($contact) {
                return $notifiable->routes['mail'] === 'primary@test.com';
            }
        );
        $this->assertEquals('sent', $proposal->fresh()->status);
    }

    public function test_it_falls_back_to_decision_maker()
    {
        Notification::fake();
        $company = Company::factory()->create();
        $contact = Contact::factory()->create(['company_id' => $company->id, 'is_decision_maker' => true, 'email' => 'dm@test.com']);
        $client = Client::factory()->create(['company_id' => $company->id, 'primary_contact_id' => null]);
        $proposal = Proposal::factory()->create(['client_id' => $client->id, 'status' => 'draft']);

        app(SendProposalAction::class)->execute($proposal);

        Notification::assertSentOnDemand(
            ProposalSentNotification::class,
            function ($notification, $channels, $notifiable) use ($contact) {
                return $notifiable->routes['mail'] === 'dm@test.com';
            }
        );
    }

    public function test_it_falls_back_to_any_contact()
    {
        Notification::fake();
        $company = Company::factory()->create();
        $contact = Contact::factory()->create(['company_id' => $company->id, 'is_decision_maker' => false, 'email' => 'any@test.com']);
        $client = Client::factory()->create(['company_id' => $company->id, 'primary_contact_id' => null]);
        $proposal = Proposal::factory()->create(['client_id' => $client->id, 'status' => 'draft']);

        app(SendProposalAction::class)->execute($proposal);

        Notification::assertSentOnDemand(
            ProposalSentNotification::class,
            function ($notification, $channels, $notifiable) use ($contact) {
                return $notifiable->routes['mail'] === 'any@test.com';
            }
        );
    }

    public function test_it_falls_back_to_opportunity_lead()
    {
        Notification::fake();
        $lead = Lead::factory()->create(['email' => 'lead@test.com']);
        $opportunity = Opportunity::factory()->create(['lead_id' => $lead->id]);
        $proposal = Proposal::factory()->create(['opportunity_id' => $opportunity->id, 'client_id' => null, 'status' => 'draft']);

        app(SendProposalAction::class)->execute($proposal);

        Notification::assertSentOnDemand(
            ProposalSentNotification::class,
            function ($notification, $channels, $notifiable) use ($lead) {
                return $notifiable->routes['mail'] === 'lead@test.com';
            }
        );
    }

    public function test_it_does_not_crash_if_no_email_found()
    {
        Notification::fake();
        $company = Company::factory()->create();
        $client = Client::factory()->create(['company_id' => $company->id, 'primary_contact_id' => null]);
        $proposal = Proposal::factory()->create(['client_id' => $client->id, 'status' => 'draft']);

        app(SendProposalAction::class)->execute($proposal);

        Notification::assertNothingSent();
        $this->assertEquals('draft', $proposal->fresh()->status); // status not updated if failed
    }
}
