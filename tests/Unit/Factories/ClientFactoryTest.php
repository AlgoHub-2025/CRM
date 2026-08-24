<?php

namespace Tests\Unit\Factories;

use Tests\TestCase;
use App\Models\Client;
use App\Models\Company;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_client_with_default_state()
    {
        $client = Client::factory()->create();

        $this->assertNotNull($client->company_id);
        $this->assertEquals('active', $client->status);
    }

    public function test_it_creates_client_with_inactive_state()
    {
        $client = Client::factory()->inactive()->create();

        $this->assertEquals('inactive', $client->status);
    }

    public function test_it_creates_client_with_primary_contact()
    {
        $client = Client::factory()->withPrimaryContact()->create();

        $this->assertNotNull($client->primary_contact_id);
        $this->assertNotNull($client->primaryContact);
        $this->assertEquals($client->company_id, $client->primaryContact->company_id);
        $this->assertTrue((bool)$client->primaryContact->is_decision_maker);
    }
}
