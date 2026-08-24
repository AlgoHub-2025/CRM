<?php

namespace Tests\Feature\Policies;

use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Client;
use App\Models\Lead;
use App\Models\Opportunity;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_manager_with_all_permission_can_view_any_client()
    {
        $manager = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $manager->id]);
        $manager->assignRole('Sales Manager');

        $client = Client::factory()->create();

        $this->assertTrue($manager->can('view', $client));
    }

    public function test_executive_with_own_permission_can_view_owned_client_via_company_account_manager()
    {
        $exec = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $exec->id]);
        $exec->assignRole('Sales Executive');

        $company = Company::factory()->create(['account_manager_id' => $employee->id]);
        $client = Client::factory()->create(['company_id' => $company->id]);

        $this->assertTrue($exec->can('view', $client));
    }

    public function test_executive_with_own_permission_can_view_owned_client_via_opportunity()
    {
        $exec = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $exec->id]);
        $exec->assignRole('Sales Executive');

        $company = Company::factory()->create();
        $lead = Lead::factory()->create(['company_id' => $company->id]);
        Opportunity::factory()->create(['lead_id' => $lead->id, 'assigned_to' => $employee->id]);
        $client = Client::factory()->create(['company_id' => $company->id]);

        $this->assertTrue($exec->can('view', $client));
    }

    public function test_executive_cannot_view_unowned_client()
    {
        $exec = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $exec->id]);
        $exec->assignRole('Sales Executive');

        $otherExec = Employee::factory()->create();
        $company = Company::factory()->create(['account_manager_id' => $otherExec->id]);
        $client = Client::factory()->create(['company_id' => $company->id]);

        $this->assertFalse($exec->can('view', $client));
    }
}
