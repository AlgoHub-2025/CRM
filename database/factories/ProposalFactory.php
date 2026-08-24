<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;
use App\Models\Opportunity;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proposal>
 */
class ProposalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proposal_number' => 'PRP-' . $this->faker->unique()->numberBetween(1000, 9999),
            // Either client_id or opportunity_id will be provided when making/creating
            'project_title' => $this->faker->sentence(3),
            'status' => $this->faker->randomElement(['draft', 'sent', 'viewed', 'negotiation', 'accepted', 'rejected', 'expired']),
            'subtotal' => 10000,
            'discount' => 0,
            'tax' => 1000,
            'total' => 11000,
            'currency' => 'USD',
            'valid_until' => now()->addDays(30),
            'payment_terms' => 'Net 30',
            'terms_and_conditions' => $this->faker->paragraphs(2, true),
        ];
    }
}
