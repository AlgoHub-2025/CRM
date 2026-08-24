<?php
namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('payments.view.global') || $user->hasPermissionTo('payments.view.own');
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->hasPermissionTo('payments.view.global')) {
            return true;
        }

        if ($user->hasPermissionTo('payments.view.own')) {
            // Defer to invoice policy for ownership
            $invoice = $payment->invoice;
            $isAccountManager = $invoice->client && $invoice->client->company && $invoice->client->company->account_manager_id === $user->employee->id;
            $isProjectManager = $invoice->project && $invoice->project->project_manager_id === $user->employee->id;
            
            return $isAccountManager || $isProjectManager;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('payments.create.global');
    }

    public function update(User $user, Payment $payment): bool
    {
        return false; // Payments are immutable, reverse them instead
    }

    public function delete(User $user, Payment $payment): bool
    {
        return false; // Strict requirement: no deletes
    }

    public function restore(User $user, Payment $payment): bool
    {
        return false;
    }

    public function forceDelete(User $user, Payment $payment): bool
    {
        return false;
    }
}
