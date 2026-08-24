<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Opportunity;
use App\Models\Proposal;
use App\Models\ProposalItem;
use App\Models\Contract;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // For testing, let's just make sure there are some Clients and Opportunities.
        $clients = Client::all();
        $opportunities = Opportunity::whereHas('stage', function($q) {
            $q->where('is_won', false);
        })->get(); // pre-sales

        if ($clients->isEmpty()) {
            return; // no clients to seed proposals for
        }

        // 1. Post-sales Proposals (linked to Clients)
        foreach ($clients->take(3) as $client) {
            $proposal = Proposal::factory()->create([
                'client_id' => $client->id,
                'opportunity_id' => null,
                'status' => 'accepted'
            ]);

            ProposalItem::factory()->count(3)->create([
                'proposal_id' => $proposal->id
            ]);

            // And a contract for it
            Contract::factory()->create([
                'client_id' => $client->id,
                'proposal_id' => $proposal->id,
                'status' => 'active'
            ]);
        }

        // 2. Pre-sales Proposals (linked to Opportunities)
        foreach ($opportunities->take(3) as $opportunity) {
            $proposal = Proposal::factory()->create([
                'client_id' => null,
                'opportunity_id' => $opportunity->id,
                'status' => 'sent'
            ]);

            ProposalItem::factory()->count(2)->create([
                'proposal_id' => $proposal->id
            ]);
        }
    }
}
