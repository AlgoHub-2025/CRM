<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Project;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class InvoicePolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Permission::findOrCreate('invoices.view.global');
        Permission::findOrCreate('invoices.view.own');
        Permission::findOrCreate('invoices.create.global');
        Permission::findOrCreate('invoices.update.global');
    }

    public function test_finance_can_view_any_invoice()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('invoices.view.global');
        
        $invoice = Invoice::factory()->create();
        $this->assertTrue($user->can('view', $invoice));
    }

    public function test_pm_can_view_own_project_invoice()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->givePermissionTo('invoices.view.own');
        
        $project = Project::factory()->create(['project_manager_id' => $employee->id]);
        $invoice = Invoice::factory()->create(['project_id' => $project->id]);
        
        $this->assertTrue($user->can('view', $invoice));
    }

    public function test_pm_cannot_view_others_invoice()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->givePermissionTo('invoices.view.own');
        
        $project = Project::factory()->create(['project_manager_id' => Employee::factory()]);
        $invoice = Invoice::factory()->create(['project_id' => $project->id]);
        
        $this->assertFalse($user->can('view', $invoice));
    }

    public function test_sales_can_view_own_client_invoice()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->givePermissionTo('invoices.view.own');
        
        $company = \App\Models\Company::factory()->create(['account_manager_id' => $employee->id]);
        $client = Client::factory()->create(['company_id' => $company->id]);
        $invoice = Invoice::factory()->create(['client_id' => $client->id]);
        
        $this->assertTrue($user->can('view', $invoice));
    }

    public function test_no_one_can_delete_invoice()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('invoices.view.global'); // Even global viewers
        
        $invoice = Invoice::factory()->create();
        
        $this->assertFalse($user->can('delete', $invoice));
    }
}
