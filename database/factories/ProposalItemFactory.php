<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Proposal;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProposalItem>
 */
class ProposalItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proposal_id' => Proposal::factory(),
            'description' => $this->faker->sentence(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'unit_price' => $this->faker->numberBetween(1000, 5000),
            'line_total' => function (array $attributes) {
                return $attributes['quantity'] * $attributes['unit_price'];
            },
            'sort_order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
