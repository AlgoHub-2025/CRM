<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Models\User;
use App\Models\Client;
use App\Models\Opportunity;

class ProposalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['proposals.view.own', 'proposals.view.all']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Proposal $proposal): bool
    {
        if ($user->hasPermissionTo('proposals.view.all')) {
            return true;
        }

        if (!$user->hasPermissionTo('proposals.view.own')) {
            return false;
        }

        return $this->ownsProposal($user, $proposal);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('proposals.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Proposal $proposal): bool
    {
        if ($user->hasPermissionTo('proposals.update.all')) {
            return true;
        }

        if (!$user->hasPermissionTo('proposals.update.own')) {
            return false;
        }

        return $this->ownsProposal($user, $proposal);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Proposal $proposal): bool
    {
        return $user->hasPermissionTo('proposals.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Proposal $proposal): bool
    {
        return $user->hasPermissionTo('proposals.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Proposal $proposal): bool
    {
        return false;
    }

    /**
     * Helper to determine ownership based on Opportunity or Client link.
     */
    private function ownsProposal(User $user, Proposal $proposal): bool
    {
        $employeeId = $user->employee?->id;
        if (!$employeeId) return false;

        // Check Opportunity Ownership
        if ($proposal->opportunity_id) {
            $opportunity = Opportunity::find($proposal->opportunity_id);
            if ($opportunity && $opportunity->assigned_to === $employeeId) {
                return true;
            }
        }

        // Check Client Ownership (same logic as ClientPolicy)
        if ($proposal->client_id) {
            $client = Client::with('company')->find($proposal->client_id);
            if ($client && $client->company) {
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

                if ($ownsLead || $ownsOpportunity) {
                    return true;
                }
            }
        }

        return false;
    }
}
