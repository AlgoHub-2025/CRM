<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['clients.view.own', 'clients.view.all']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Client $client): bool
    {
        if ($user->hasPermissionTo('clients.view.all')) {
            return true;
        }

        if (!$user->hasPermissionTo('clients.view.own')) {
            return false;
        }

        // Ownership: User is account manager for the company, or owns a lead/opportunity tied to the company
        if ($client->company && $client->company->account_manager_id === $user->employee->id) {
            return true;
        }

        $ownsLead = \App\Models\Lead::where('company_id', $client->company_id)
            ->where('assigned_to', $user->employee->id)
            ->exists();
            
        $ownsOpportunity = \App\Models\Opportunity::whereHas('lead', function($q) use ($client) {
                $q->where('company_id', $client->company_id);
            })
            ->where('assigned_to', $user->employee->id)
            ->exists();

        return $ownsLead || $ownsOpportunity;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('clients.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Client $client): bool
    {
        if ($user->hasPermissionTo('clients.update.all')) {
            return true;
        }

        if (!$user->hasPermissionTo('clients.update.own')) {
            return false;
        }

        return $this->view($user, $client); // Same ownership logic as view
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Client $client): bool
    {
        return $user->hasPermissionTo('clients.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Client $client): bool
    {
        return $user->hasPermissionTo('clients.delete'); // using delete for restore permission
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Client $client): bool
    {
        return false; // Nobody force deletes yet
    }
}
