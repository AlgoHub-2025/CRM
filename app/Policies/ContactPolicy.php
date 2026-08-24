<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContactPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('contacts.view.all') || $user->hasPermissionTo('contacts.view.own');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Contact $contact): bool
    {
        if ($user->hasPermissionTo('contacts.view.all')) {
            return true;
        }

        if ($user->hasPermissionTo('contacts.view.own')) {
            return $this->userOwnsContact($user, $contact);
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('contacts.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Contact $contact): bool
    {
        if ($user->hasPermissionTo('contacts.update.all')) {
            return true;
        }

        if ($user->hasPermissionTo('contacts.update.own')) {
            return $this->userOwnsContact($user, $contact);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Contact $contact): bool
    {
        return $user->hasPermissionTo('contacts.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Contact $contact): bool
    {
        return $user->hasPermissionTo('contacts.delete');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Contact $contact): bool
    {
        return $user->hasPermissionTo('contacts.delete');
    }

    /**
     * Helper to determine if a user "owns" a contact.
     * A user owns a contact if they are the account manager for the company,
     * or if they are assigned to any lead or opportunity under that company.
     */
    private function userOwnsContact(User $user, Contact $contact): bool
    {
        $company = $contact->company;

        if (!$company) {
            return false;
        }

        if ($company->account_manager_id === $user->employee_id) {
            return true;
        }

        $isLeadOwner = $company->leads()->where('assigned_to', $user->employee_id)->exists();
        if ($isLeadOwner) {
            return true;
        }

        $isOpportunityOwner = \App\Models\Opportunity::where('assigned_to', $user->employee_id)
            ->where(function ($query) use ($company) {
                $query->whereHas('lead', function ($q) use ($company) {
                    $q->where('company_id', $company->id);
                })->orWhereHas('client', function ($q) use ($company) {
                    $q->where('company_id', $company->id);
                });
            })->exists();

        if ($isOpportunityOwner) {
            return true;
        }

        return false;
    }
}
