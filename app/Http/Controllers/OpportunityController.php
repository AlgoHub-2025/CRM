<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opportunity;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OpportunityController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('viewAny', Opportunity::class);
        return view('opportunities.index');
    }

    public function markWon(Request $request, Opportunity $opportunity, \App\Actions\Opportunities\MarkOpportunityWonAction $action)
    {
        $this->authorize('update', $opportunity);
        
        $request->validate([
            'stage_id' => 'required|exists:pipeline_stages,id'
        ]);

        try {
            $client = $action->execute($opportunity, $request->stage_id);
            return redirect()->route('clients.show', $client)->with('success', 'Opportunity marked as won and client created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
