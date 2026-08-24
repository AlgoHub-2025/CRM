<?php

namespace App\Policies;

use App\Models\Contract;
use App\Models\User;
use App\Models\Client;

class ContractPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['contracts.view.own', 'contracts.view.all']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Contract $contract): bool
    {
        if ($user->hasPermissionTo('contracts.view.all')) {
            return true;
        }

        if (!$user->hasPermissionTo('contracts.view.own')) {
            return false;
        }

        return $this->ownsContract($user, $contract);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('contracts.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Contract $contract): bool
    {
        if ($user->hasPermissionTo('contracts.update.all')) {
            return true;
        }

        if (!$user->hasPermissionTo('contracts.update.own')) {
            return false;
        }

        return $this->ownsContract($user, $contract);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Contract $contract): bool
    {
        return $user->hasPermissionTo('contracts.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Contract $contract): bool
    {
        return $user->hasPermissionTo('contracts.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Contract $contract): bool
    {
        return false;
    }

    /**
     * Helper to determine ownership based on Client link.
     */
    private function ownsContract(User $user, Contract $contract): bool
    {
        $employeeId = $user->employee?->id;
        if (!$employeeId) return false;

        $client = Client::with('company')->find($contract->client_id);
        if (!$client || !$client->company) return false;

        if ($client->company->account_manager_id === $employeeId) {
            return true;
        }

        $ownsLead = \App\Models\Lead::where('company_id', $client->company_id)
            ->where('assigned_to', $employeeId)
            ->exists();
            
        $ownsOpportunity = \App\Models\Opportunity::whereHas('lead', function($q) use ($client) {
                $q->where('company_id', $client->company_id);
            })
            ->where('assigned_to', $employeeId)
            ->exists();

        return $ownsLead || $ownsOpportunity;
    }
}
