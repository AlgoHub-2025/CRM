<?php

namespace Database\Factories;

use App\Models\TicketMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketMessage>
 */
class TicketMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => \App\Models\SupportTicket::factory(),
            'sender_type' => 'client', // default, can be employee
            'sender_id' => \App\Models\Client::factory(),
            'logged_by_employee_id' => null, // Optional proxy
            'message' => $this->faker->paragraph(),
        ];
    }
}
