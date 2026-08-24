<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Proposals\ProposalBuilder;
use Livewire\Livewire;
use Tests\TestCase;

class ProposalBuilderTest extends TestCase
{
    public function test_can_reorder_items_and_persist_to_db()
    {
        $client = \App\Models\Client::factory()->create();

        Livewire::test(ProposalBuilder::class)
            ->set('title', 'Test Proposal')
            ->set('client_id', $client->id)
            ->set('items', [
                ['description' => 'Item A', 'quantity' => 1, 'unit_price' => 100],
                ['description' => 'Item B', 'quantity' => 2, 'unit_price' => 200],
                ['description' => 'Item C', 'quantity' => 3, 'unit_price' => 300],
            ])
            // Send new order: C (index 2), A (index 0), B (index 1)
            ->call('updateOrder', [2, 0, 1])
            ->call('save');

        $proposal = \App\Models\Proposal::first();
        
        $this->assertDatabaseHas('proposal_items', [
            'proposal_id' => $proposal->id,
            'description' => 'Item C',
            'sort_order' => 1
        ]);

        $this->assertDatabaseHas('proposal_items', [
            'proposal_id' => $proposal->id,
            'description' => 'Item A',
            'sort_order' => 2
        ]);

        $this->assertDatabaseHas('proposal_items', [
            'proposal_id' => $proposal->id,
            'description' => 'Item B',
            'sort_order' => 3
        ]);
    }
}
