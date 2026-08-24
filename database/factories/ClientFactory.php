<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => \App\Models\Company::factory(),
            'status' => 'active',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function withPrimaryContact(): static
    {
        return $this->afterCreating(function (\App\Models\Client $client) {
            $contact = \App\Models\Contact::factory()->create([
                'company_id' => $client->company_id,
                'is_decision_maker' => true,
            ]);
            
            $client->update(['primary_contact_id' => $contact->id]);
        });
    }
}
