<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Proposal;
use App\Models\Client;
use Spatie\Permission\Models\Permission;

class ProposalAcceptTest extends TestCase
{
    use RefreshDatabase;

    public function test_accepting_proposal_creates_contract()
    {
        $user = User::factory()->create();
        Permission::firstOrCreate(['name' => 'proposals.update.global']);
        Permission::firstOrCreate(['name' => 'proposals.update.all']);
        $user->givePermissionTo('proposals.update.global');
        $user->givePermissionTo('proposals.update.all');
        
        $client = Client::factory()->create();
        $proposal = Proposal::factory()->create([
            'client_id' => $client->id,
            'status' => 'sent',
            'total' => 5000,
            'payment_terms' => 'Net 30'
        ]);

        $this->actingAs($user)
             ->post(route('proposals.accept', $proposal))
             ->assertRedirect()
             ->assertSessionHas('success');

        $this->assertEquals('accepted', $proposal->fresh()->status);
        
        $this->assertDatabaseHas('contracts', [
            'client_id' => $client->id,
            'proposal_id' => $proposal->id,
            'value' => 5000,
            'payment_terms' => 'Net 30',
            'status' => 'draft',
        ]);
    }
}
