<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Invoice;
use App\Models\User;
use App\Actions\Payments\RecordPaymentAction;

class RecordPaymentActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_partial_payment_updates_status_to_partially_paid()
    {
        $user = User::factory()->create();
        $employee = \App\Models\Employee::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $invoice = Invoice::factory()->create([
            'total' => 100000,
            'paid_amount' => 0,
            'status' => 'draft'
        ]);

        $action = new RecordPaymentAction();
        $action->execute($invoice, [
            'amount' => 40000,
            'method' => 'bank_transfer',
        ]);

        $invoice->refresh();
        $this->assertEquals(40000, $invoice->paid_amount);
        $this->assertEquals('partially_paid', $invoice->status);
    }

    public function test_full_payment_updates_status_to_paid()
    {
        $user = User::factory()->create();
        $employee = \App\Models\Employee::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $invoice = Invoice::factory()->create([
            'total' => 100000,
            'paid_amount' => 40000,
            'status' => 'partially_paid'
        ]);

        $action = new RecordPaymentAction();
        $action->execute($invoice, [
            'amount' => 60000,
            'method' => 'bank_transfer',
        ]);

        $invoice->refresh();
        $this->assertEquals(100000, $invoice->paid_amount);
        $this->assertEquals('paid', $invoice->status);
    }

    public function test_overpayment_caps_status_to_paid()
    {
        $user = User::factory()->create();
        $employee = \App\Models\Employee::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $invoice = Invoice::factory()->create([
            'total' => 100000,
            'paid_amount' => 0,
            'status' => 'draft'
        ]);

        $action = new RecordPaymentAction();
        $action->execute($invoice, [
            'amount' => 150000,
            'method' => 'bank_transfer',
        ]);

        $invoice->refresh();
        $this->assertEquals(150000, $invoice->paid_amount);
        $this->assertEquals('paid', $invoice->status);
    }
}
