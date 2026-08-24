<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Actions\Leads\ConvertLeadAction;
use Illuminate\Http\Request;

class LeadConversionController extends Controller
{
    public function convert(Lead $lead, ConvertLeadAction $action)
    {
        $this->authorize('update', $lead);

        if ($lead->converted_at) {
            return back()->with('error', 'This lead has already been converted.');
        }

        try {
            $opportunity = $action->execute($lead);
            // We do not have a dedicated opportunities.show page yet, so we redirect to opportunities.index
            return redirect()->route('opportunities.index')->with('success', 'Lead successfully converted to Opportunity!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to convert lead: ' . $e->getMessage());
        }
    }
}
