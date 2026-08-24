<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Project;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class SupportTicketPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Permission::firstOrCreate(['name' => 'tickets.view.global']);
        Permission::firstOrCreate(['name' => 'tickets.view.own']);
        Permission::firstOrCreate(['name' => 'tickets.update.global']);
        Permission::firstOrCreate(['name' => 'tickets.update.own']);
    }

    public function test_global_user_can_view_any_ticket()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('tickets.view.global');

        $ticket = SupportTicket::factory()->create();

        $this->assertTrue($user->can('view', $ticket));
    }

    public function test_pm_can_view_ticket_for_their_project()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->givePermissionTo('tickets.view.own');

        $project = Project::factory()->create(['project_manager_id' => $employee->id]);
        $ticket = SupportTicket::factory()->create(['project_id' => $project->id]);

        $this->assertTrue($user->can('view', $ticket));
    }

    public function test_pm_cannot_view_ticket_for_other_project()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->givePermissionTo('tickets.view.own');

        $project = Project::factory()->create();
        $ticket = SupportTicket::factory()->create(['project_id' => $project->id]);

        $this->assertFalse($user->can('view', $ticket));
    }

    public function test_sales_can_view_ticket_for_their_client()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->givePermissionTo('tickets.view.own');

        $company = Company::factory()->create(['account_manager_id' => $employee->id]);
        $client = Client::factory()->create(['company_id' => $company->id]);
        $ticket = SupportTicket::factory()->create(['client_id' => $client->id]);

        $this->assertTrue($user->can('view', $ticket));
    }

    public function test_assignee_can_view_ticket()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->givePermissionTo('tickets.view.own');

        $ticket = SupportTicket::factory()->create(['assigned_to' => $employee->id]);

        $this->assertTrue($user->can('view', $ticket));
    }
}
