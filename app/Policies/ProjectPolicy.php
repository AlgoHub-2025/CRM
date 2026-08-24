<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $employee = $user->employee;
        return $employee && ($user->hasPermissionTo('projects.view.all') || $user->hasPermissionTo('projects.view.own'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Project $project): bool
    {
        $employee = $user->employee;
        if (!$employee) return false;

        if ($user->hasPermissionTo('projects.view.all')) {
            return true;
        }

        if ($user->hasPermissionTo('projects.view.own')) {
            if ($project->project_manager_id === $employee->id) {
                return true;
            }
            // Fallback for Sales Exec: if they can view the client (via ownership)
            // We ensure they don't have clients.view.all, so PMs don't accidentally get access to all projects
            if ($project->client && !$user->hasPermissionTo('clients.view.all') && $user->can('view', $project->client)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $employee = $user->employee;
        return $employee && $user->hasPermissionTo('projects.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Project $project): bool
    {
        $employee = $user->employee;
        if (!$employee) return false;

        if ($user->hasPermissionTo('projects.update.all')) {
            return true;
        }

        if ($user->hasPermissionTo('projects.update.own')) {
            if ($project->project_manager_id === $employee->id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Project $project): bool
    {
        $employee = $user->employee;
        return $employee && $user->hasPermissionTo('projects.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Project $project): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return false;
    }
}
