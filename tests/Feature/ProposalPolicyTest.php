<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Client;
use App\Models\Company;
use App\Models\Opportunity;
use App\Models\Proposal;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ProposalPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup permissions
        Permission::firstOrCreate(['name' => 'proposals.view.own']);
        Permission::firstOrCreate(['name' => 'proposals.view.all']);
    }

    public function test_user_can_view_proposal_tied_to_their_opportunity()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->givePermissionTo('proposals.view.own');

        $opportunity = Opportunity::factory()->create(['assigned_to' => $employee->id]);
        $proposal = Proposal::factory()->create(['opportunity_id' => $opportunity->id, 'client_id' => null]);

        $this->assertTrue($user->can('view', $proposal));
    }

    public function test_user_cannot_view_proposal_tied_to_others_opportunity()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->givePermissionTo('proposals.view.own');

        $otherEmployee = Employee::factory()->create();
        $opportunity = Opportunity::factory()->create(['assigned_to' => $otherEmployee->id]);
        $proposal = Proposal::factory()->create(['opportunity_id' => $opportunity->id, 'client_id' => null]);

        $this->assertFalse($user->can('view', $proposal));
    }

    public function test_user_can_view_proposal_tied_to_client_they_account_manage()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->givePermissionTo('proposals.view.own');

        $company = Company::factory()->create(['account_manager_id' => $employee->id]);
        $client = Client::factory()->create(['company_id' => $company->id]);
        $proposal = Proposal::factory()->create(['client_id' => $client->id, 'opportunity_id' => null]);

        $this->assertTrue($user->can('view', $proposal));
    }

    public function test_user_with_view_all_can_view_any_proposal()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->givePermissionTo('proposals.view.all');

        $otherEmployee = Employee::factory()->create();
        $opportunity = Opportunity::factory()->create(['assigned_to' => $otherEmployee->id]);
        $proposal = Proposal::factory()->create(['opportunity_id' => $opportunity->id, 'client_id' => null]);

        $this->assertTrue($user->can('view', $proposal));
    }
}
