<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Client;
use Spatie\Permission\Models\Role;

class ProjectPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_manager_can_view_own_project()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->assignRole('Project Manager');

        $project = Project::factory()->create(['project_manager_id' => $employee->id]);
        
        $this->assertTrue($user->can('view', $project));
    }

    public function test_project_manager_cannot_view_unassigned_project()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->assignRole('Project Manager');

        $project = Project::factory()->create(['project_manager_id' => null]);
        
        $this->assertFalse($user->can('view', $project));
    }

    public function test_sales_exec_can_view_project_if_they_own_client()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->assignRole('Sales Executive');

        // Sales Exec owns the client via company
        $company = \App\Models\Company::factory()->create(['account_manager_id' => $employee->id]);
        $client = Client::factory()->create(['company_id' => $company->id]);
        $project = Project::factory()->create(['client_id' => $client->id, 'project_manager_id' => null]);
        
        $this->assertTrue($user->can('view', $project));
    }
}
