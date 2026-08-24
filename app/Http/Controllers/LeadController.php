<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Company;
use App\Models\LeadSource;
use App\Models\PipelineStage;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Actions\Leads\CreateLeadAction;
use App\Actions\Leads\UpdateLeadAction;

class LeadController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Lead::class);
        return view('leads.index');
    }

    public function create()
    {
        $this->authorize('create', Lead::class);
        $companies = Company::orderBy('name')->get(['id', 'name']);
        $sources = LeadSource::orderBy('name')->get(['id', 'name']);
        $stages = PipelineStage::where('type', 'lead')->orderBy('order')->get(['id', 'name']);
        $employees = Employee::with('user:id,name')->orderBy('id')->get(['id', 'employee_code', 'designation', 'user_id']);
        return view('leads.create', compact('companies', 'sources', 'stages', 'employees'));
    }

    public function store(StoreLeadRequest $request, CreateLeadAction $action)
    {
        $this->authorize('create', Lead::class);
        $lead = $action->execute($request->validated());
        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);
        $lead->load(['company', 'source', 'status', 'assignedTo']);
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $this->authorize('update', $lead);
        $companies = Company::orderBy('name')->get(['id', 'name']);
        $sources = LeadSource::orderBy('name')->get(['id', 'name']);
        $stages = PipelineStage::where('type', 'lead')->orderBy('order')->get(['id', 'name']);
        $employees = Employee::with('user:id,name')->orderBy('id')->get(['id', 'employee_code', 'designation', 'user_id']);
        return view('leads.edit', compact('lead', 'companies', 'sources', 'stages', 'employees'));
    }

    public function update(UpdateLeadRequest $request, Lead $lead, UpdateLeadAction $action)
    {
        $this->authorize('update', $lead);
        $action->execute($lead, $request->validated());
        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy(Lead $lead)
    {
        $this->authorize('delete', $lead);
        $lead->delete();
        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }
}
