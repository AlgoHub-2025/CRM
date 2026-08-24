<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Actions\Contracts\ActivateContractAction;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        $this->authorize('view', $contract);
        
        $contract->load(['client.company', 'proposal', 'projects']);
        
        return view('contracts.show', compact('contract'));
    }

    /**
     * Activate a contract and create a project from it.
     */
    public function activate(Contract $contract, ActivateContractAction $action)
    {
        $this->authorize('update', $contract);

        $project = $action->execute($contract);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Contract activated and project created successfully.');
    }
}

