<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;

class CompanyController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Company::class);
        return view('companies.index');
    }

    public function create()
    {
        $this->authorize('create', Company::class);
        $employees = Employee::orderBy('id')->get(['id', 'employee_code', 'designation']);
        return view('companies.create', compact('employees'));
    }

    public function store(StoreCompanyRequest $request, \App\Actions\Companies\CreateCompanyAction $action)
    {
        $this->authorize('create', Company::class);
        $company = $action->execute($request->validated());
        return redirect()->route('companies.index')->with('success', 'Company created successfully.');
    }

    public function show(Company $company)
    {
        $this->authorize('view', $company);
        $company->load('contacts');
        return view('companies.show', compact('company'));
    }

    public function edit(Company $company)
    {
        $this->authorize('update', $company);
        $employees = Employee::orderBy('id')->get(['id', 'employee_code', 'designation']);
        return view('companies.edit', compact('company', 'employees'));
    }

    public function update(UpdateCompanyRequest $request, Company $company, \App\Actions\Companies\UpdateCompanyAction $action)
    {
        $this->authorize('update', $company);
        $action->execute($company, $request->validated());
        return redirect()->route('companies.index')->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        $this->authorize('delete', $company);
        $company->delete();
        return redirect()->route('companies.index')->with('success', 'Company deleted successfully.');
    }
}
