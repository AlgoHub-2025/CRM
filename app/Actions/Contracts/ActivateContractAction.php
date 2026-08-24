<?php

namespace App\Actions\Contracts;

use App\Models\Contract;
use App\Models\Project;
use App\Events\AuditableAction;
use Illuminate\Support\Facades\DB;

class ActivateContractAction
{
    /**
     * Activate a contract and spin up a Project from it.
     * Uses check-and-reuse to prevent duplicate Projects for the same Contract.
     */
    public function execute(Contract $contract): Project
    {
        // Duplicate prevention: if a project already exists for this contract, return it
        $existing = Project::where('contract_id', $contract->id)->first();
        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($contract) {
            $oldValues = $contract->only(['status']);

            // Double-check inside the transaction (race-safe)
            $existing = Project::lockForUpdate()->where('contract_id', $contract->id)->first();
            if ($existing) {
                return $existing;
            }

            $project = Project::create([
                'name' => $contract->proposal?->project_title ?? ('Project for ' . ($contract->client->company->name ?? 'Client')),
                'client_id' => $contract->client_id,
                'contract_id' => $contract->id,
                'budget' => $contract->value ?? 0,
                'status' => 'not_started',
                'description' => $contract->scope ?? null,
            ]);

            // Update contract status to active
            $contract->update(['status' => 'active']);

            AuditableAction::dispatch(
                $contract,
                'activated',
                'Contracts',
                $oldValues,
                $contract->only(['status'])
            );

            return $project;
        });
    }
}
