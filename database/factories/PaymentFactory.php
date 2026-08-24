<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'client_id' => Client::factory(),
            'amount' => 100000,
            'method' => 'bank_transfer',
            'transaction_reference' => 'TXN-' . strtoupper(Str::random(8)),
            'paid_at' => now(),
            'received_by' => Employee::factory(),
        ];
    }
}
