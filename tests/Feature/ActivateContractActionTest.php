<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Project;
use App\Actions\Contracts\ActivateContractAction;

class ActivateContractActionTest extends TestCase
{
    use RefreshDatabase;

    private function createContractWithClient(): Contract
    {
        $client = Client::factory()->create();
        return Contract::factory()->create([
            'client_id' => $client->id,
            'value' => 5000000, // $50,000 in cents
            'status' => 'draft',
        ]);
    }

    public function test_activating_contract_creates_project_with_correct_fields()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $contract = $this->createContractWithClient();

        $action = new ActivateContractAction();
        $project = $action->execute($contract);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals($contract->client_id, $project->client_id);
        $this->assertEquals($contract->id, $project->contract_id);
        $this->assertEquals($contract->value, $project->budget);
        $this->assertEquals('not_started', $project->status);
    }

    public function test_activating_contract_updates_contract_status_to_active()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $contract = $this->createContractWithClient();

        $action = new ActivateContractAction();
        $action->execute($contract);

        $contract->refresh();
        $this->assertEquals('active', $contract->status);
    }

    public function test_duplicate_activation_returns_existing_project()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $contract = $this->createContractWithClient();

        $action = new ActivateContractAction();
        $project1 = $action->execute($contract);
        $project2 = $action->execute($contract);

        $this->assertEquals($project1->id, $project2->id);
        $this->assertEquals(1, Project::where('contract_id', $contract->id)->count());
    }
}
