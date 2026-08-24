<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Lead;
use App\Models\PipelineStage;
use App\Models\Employee;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Opportunity>
 */
class OpportunityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'lead_id' => Lead::factory(),
            'value' => fake()->numberBetween(1000, 100000),
            'probability' => fake()->numberBetween(10, 90),
            'stage_id' => PipelineStage::factory(),
        ];
    }
}
