<?php

namespace App\Actions\Projects;

use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;

class CreateMilestoneAction
{
    /**
     * Create a new milestone for a project with correct order calculation.
     */
    public function execute(Project $project, array $data): Milestone
    {
        Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
        ])->validate();

        // Order scoped globally per project
        $maxOrder = Milestone::where('project_id', $project->id)->max('order') ?? 0;

        return Milestone::create([
            'project_id' => $project->id,
            'name' => $data['name'],
            'due_date' => $data['due_date'] ?? null,
            'order' => $maxOrder + 1,
            'status' => 'pending',
        ]);
    }
}
