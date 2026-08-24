<?php

namespace App\Policies;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SupportTicketPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('tickets.view.global') || $user->can('tickets.view.own');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SupportTicket $supportTicket): bool
    {
        if ($user->can('tickets.view.global')) {
            return true;
        }

        if (!$user->can('tickets.view.own')) {
            return false;
        }

        $employeeId = $user->employee->id ?? null;
        if (!$employeeId) {
            return false;
        }

        return $supportTicket->assigned_to === $employeeId ||
               ($supportTicket->project && $supportTicket->project->project_manager_id === $employeeId) ||
               ($supportTicket->client && $supportTicket->client->company && $supportTicket->client->company->account_manager_id === $employeeId);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('tickets.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SupportTicket $supportTicket): bool
    {
        if ($user->can('tickets.update.global')) {
            return true;
        }

        if (!$user->can('tickets.update.own')) {
            return false;
        }

        $employeeId = $user->employee->id ?? null;
        if (!$employeeId) {
            return false;
        }

        return $supportTicket->assigned_to === $employeeId ||
               ($supportTicket->project && $supportTicket->project->project_manager_id === $employeeId) ||
               ($supportTicket->client && $supportTicket->client->company && $supportTicket->client->company->account_manager_id === $employeeId);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SupportTicket $supportTicket): bool
    {
        // Support tickets should generally not be deleted, or only by global admin
        return $user->can('tickets.delete.global');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SupportTicket $supportTicket): bool
    {
        return $user->can('tickets.delete.global');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SupportTicket $supportTicket): bool
    {
        return false; // Nobody should permanently delete tickets
    }
}
