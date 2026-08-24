<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Client;
use App\Models\Proposal;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contract_number' => 'CON-' . $this->faker->unique()->numberBetween(1000, 9999),
            'client_id' => Client::factory(),
            'proposal_id' => null, // Optional
            'start_date' => now(),
            'end_date' => now()->addYear(),
            'value' => $this->faker->numberBetween(10000, 100000),
            'payment_terms' => 'Net 30',
            'scope' => $this->faker->paragraphs(3, true),
            'status' => $this->faker->randomElement(['draft', 'active', 'completed', 'terminated']),
        ];
    }
}
