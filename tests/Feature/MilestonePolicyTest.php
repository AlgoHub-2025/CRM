<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Employee;
use App\Models\Project;
use App\Models\Milestone;

class MilestonePolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_manager_can_manage_milestone()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->assignRole('Project Manager');

        $project = Project::factory()->create(['project_manager_id' => $employee->id]);
        $milestone = Milestone::factory()->create(['project_id' => $project->id]);
        
        $this->assertTrue($user->can('update', $milestone));
    }
}
