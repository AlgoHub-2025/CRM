<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Implementation',
            'client_id' => \App\Models\Client::factory(),
            'status' => 'not_started',
            'description' => fake()->paragraph(),
            'budget' => fake()->numberBetween(10000, 100000),
        ];
    }
}
