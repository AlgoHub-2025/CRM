<?php

namespace Database\Factories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\Project;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_number' => 'INV-' . strtoupper(Str::random(6)),
            'client_id' => Client::factory(),
            'project_id' => Project::factory(),
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => 100000,
            'discount' => 0,
            'tax' => 0,
            'total' => 100000,
            'paid_amount' => 0,
            'status' => 'draft',
        ];
    }
}
