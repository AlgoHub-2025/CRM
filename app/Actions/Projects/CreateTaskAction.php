<?php

namespace App\Actions\Projects;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;

class CreateTaskAction
{
    /**
     * Create a new task for a project with correct order calculation.
     * Order is scoped per status column within the project (matching Kanban behavior).
     */
    public function execute(Project $project, array $data): Task
    {
        Validator::make($data, [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'assigned_to' => ['nullable', 'string', 'exists:employees,id'],
            'milestone_id' => ['nullable', 'string', 'exists:milestones,id'],
            'deadline' => ['nullable', 'date'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'status' => ['nullable', 'in:todo,in_progress,review,completed,blocked'],
        ])->validate();

        $status = $data['status'] ?? 'todo';

        // Order scoped per status column within the project
        $maxOrder = Task::where('project_id', $project->id)
            ->where('status', $status)
            ->max('order') ?? 0;

        return Task::create([
            'project_id' => $project->id,
            'milestone_id' => $data['milestone_id'] ?? null,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'assigned_to' => $data['assigned_to'] ?? null,
            'priority' => $data['priority'],
            'deadline' => $data['deadline'] ?? null,
            'status' => $status,
            'order' => $maxOrder + 1,
        ]);
    }
}
