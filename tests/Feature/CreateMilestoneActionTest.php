<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Project;
use App\Models\Milestone;
use App\Actions\Projects\CreateMilestoneAction;

class CreateMilestoneActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_milestone_with_correct_order()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $project = Project::factory()->create();

        $action = new CreateMilestoneAction();

        $m1 = $action->execute($project, ['name' => 'Milestone 1']);
        $m2 = $action->execute($project, ['name' => 'Milestone 2']);
        $m3 = $action->execute($project, ['name' => 'Milestone 3']);

        $this->assertEquals(1, $m1->order);
        $this->assertEquals(2, $m2->order);
        $this->assertEquals(3, $m3->order);
    }

    public function test_order_is_scoped_per_project()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        $action = new CreateMilestoneAction();

        $action->execute($projectA, ['name' => 'A-1']);
        $action->execute($projectA, ['name' => 'A-2']);
        $m_b1 = $action->execute($projectB, ['name' => 'B-1']);

        // Project B's first milestone should start at order 1, not 3
        $this->assertEquals(1, $m_b1->order);
    }

    public function test_milestone_belongs_to_correct_project()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
        $project = Project::factory()->create();

        $action = new CreateMilestoneAction();
        $milestone = $action->execute($project, [
            'name' => 'Design Phase',
            'due_date' => '2026-09-15',
        ]);

        $this->assertEquals($project->id, $milestone->project_id);
        $this->assertEquals('Design Phase', $milestone->name);
        $this->assertEquals('2026-09-15', $milestone->due_date->format('Y-m-d'));
        $this->assertEquals('pending', $milestone->status);
    }
}
