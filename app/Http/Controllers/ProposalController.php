<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Proposal $proposal)
    {
        $this->authorize('view', $proposal);
        
        $proposal->load(['client.company', 'opportunity', 'items']);
        
        return view('proposals.show', compact('proposal'));
    }

    /**
     * Accept the proposal and draft a contract.
     */
    public function accept(Proposal $proposal, \App\Actions\Proposals\AcceptProposalAction $action)
    {
        $this->authorize('update', $proposal);

        try {
            $action->execute($proposal);
            return back()->with('success', 'Proposal accepted successfully! A draft contract has been generated.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function send(Proposal $proposal, \App\Actions\Proposals\SendProposalAction $action)
    {
        $this->authorize('update', $proposal);

        try {
            $action->execute($proposal);
            return back()->with('success', 'Proposal sent to client successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
