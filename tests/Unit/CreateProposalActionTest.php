<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Actions\Proposals\CreateProposalAction;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

class CreateProposalActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_proposal_and_calculates_totals()
    {
        $client = Client::factory()->create();
        
        $data = [
            'client_id' => $client->id,
            'project_title' => 'Test Project',
            'discount' => 100,
            'tax' => 50,
        ];

        $items = [
            ['description' => 'Item 1', 'quantity' => 2, 'unit_price' => 500, 'sort_order' => 1],
            ['description' => 'Item 2', 'quantity' => 1, 'unit_price' => 200, 'sort_order' => 2],
        ];

        $action = new CreateProposalAction();
        $proposal = $action->execute($data, $items);

        $this->assertDatabaseHas('proposals', [
            'id' => $proposal->id,
            'client_id' => $client->id,
            'subtotal' => 1200, // (2*500) + (1*200)
            'total' => 1150, // 1200 - 100 + 50
        ]);

        $this->assertDatabaseHas('proposal_items', [
            'proposal_id' => $proposal->id,
            'description' => 'Item 1',
            'line_total' => 1000,
        ]);
    }

    public function test_fails_if_neither_client_nor_opportunity_provided()
    {
        $this->expectException(ValidationException::class);
        
        $data = [
            'project_title' => 'Test Project',
        ];

        $action = new CreateProposalAction();
        $action->execute($data, []);
    }
}
