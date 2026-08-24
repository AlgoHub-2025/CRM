<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $employee = $user->employee;
        return $employee && ($user->hasPermissionTo('tasks.view.all') || $user->hasPermissionTo('tasks.view.own'));
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        $employee = $user->employee;
        if (!$employee) return false;

        if ($user->hasPermissionTo('tasks.view.all')) {
            return true;
        }

        if ($user->hasPermissionTo('tasks.view.own')) {
            if ($task->assigned_to === $employee->id) {
                return true;
            }
            // Or if they can view the parent project (e.g., PM or Sales Exec)
            if ($user->can('view', $task->project)) {
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
        return $employee && $user->hasPermissionTo('tasks.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        $employee = $user->employee;
        if (!$employee) return false;

        if ($user->hasPermissionTo('tasks.update.all')) {
            return true;
        }

        if ($user->hasPermissionTo('tasks.update.own')) {
            if ($task->assigned_to === $employee->id) {
                return true;
            }
            // Or if they can update the parent project (e.g., PM)
            if ($user->can('update', $task->project)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        $employee = $user->employee;
        return $employee && $user->hasPermissionTo('tasks.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}
