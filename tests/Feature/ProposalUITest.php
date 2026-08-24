<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Proposal;
use App\Models\Client;
use App\Models\Company;
use App\Models\Contact;
use App\Notifications\ProposalSentNotification;
use Illuminate\Support\Facades\Notification;

class ProposalUITest extends TestCase
{
    use RefreshDatabase;

    public function test_ui_button_sends_proposal()
    {
        Notification::fake();

        \Spatie\Permission\Models\Permission::findOrCreate('proposals.update.all');
        $user = User::factory()->create();
        $user->givePermissionTo('proposals.update.all');
        
        $company = Company::factory()->create();
        $contact = Contact::factory()->create(['company_id' => $company->id, 'email' => 'client@test.com']);
        $client = Client::factory()->create(['company_id' => $company->id, 'primary_contact_id' => $contact->id]);
        
        $proposal = Proposal::factory()->create([
            'client_id' => $client->id,
            'status' => 'draft'
        ]);

        $response = $this->actingAs($user)
                         ->from(route('proposals.show', $proposal))
                         ->post(route('proposals.send', $proposal));

        $response->assertRedirect(route('proposals.show', $proposal));
        $response->assertSessionHas('success', 'Proposal sent to client successfully!');

        Notification::assertSentOnDemand(
            ProposalSentNotification::class,
            function ($notification, $channels, $notifiable) use ($contact) {
                return $notifiable->routes['mail'] === 'client@test.com';
            }
        );

        $this->assertEquals('sent', $proposal->fresh()->status);
    }
}
