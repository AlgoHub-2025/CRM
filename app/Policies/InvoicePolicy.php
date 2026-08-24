<?php
namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('invoices.view.global') || $user->hasPermissionTo('invoices.view.own');
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($user->hasPermissionTo('invoices.view.global')) {
            return true;
        }

        if ($user->hasPermissionTo('invoices.view.own')) {
            $isAccountManager = $invoice->client && $invoice->client->company && $invoice->client->company->account_manager_id === $user->employee->id;
            $isProjectManager = $invoice->project && $invoice->project->project_manager_id === $user->employee->id;
            
            return $isAccountManager || $isProjectManager;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('invoices.create.global');
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->hasPermissionTo('invoices.update.global');
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return false; // Strict requirement: no deletes
    }

    public function restore(User $user, Invoice $invoice): bool
    {
        return false;
    }

    public function forceDelete(User $user, Invoice $invoice): bool
    {
        return false;
    }
}
