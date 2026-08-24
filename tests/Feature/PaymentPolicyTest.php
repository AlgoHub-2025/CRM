<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Client;
use Spatie\Permission\Models\Permission;

class PaymentPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::findOrCreate('payments.view.global');
        Permission::findOrCreate('payments.view.own');
        Permission::findOrCreate('payments.create.global');
    }

    public function test_finance_can_view_any_payment()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('payments.view.global');
        
        $payment = Payment::factory()->create();
        $this->assertTrue($user->can('view', $payment));
    }

    public function test_sales_can_view_own_client_payment()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->givePermissionTo('payments.view.own');
        
        $company = \App\Models\Company::factory()->create(['account_manager_id' => $employee->id]);
        $client = Client::factory()->create(['company_id' => $company->id]);
        $invoice = Invoice::factory()->create(['client_id' => $client->id]);
        $payment = Payment::factory()->create(['invoice_id' => $invoice->id]);
        
        $this->assertTrue($user->can('view', $payment));
    }

    public function test_no_one_can_update_or_delete_payment()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('payments.view.global');
        
        $payment = Payment::factory()->create();
        
        $this->assertFalse($user->can('update', $payment));
        $this->assertFalse($user->can('delete', $payment));
    }
}
