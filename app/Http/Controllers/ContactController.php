<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Company;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function create(Company $company)
    {
        $this->authorize('create', Contact::class);
        return view('contacts.create', compact('company'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreContactRequest $request, Company $company, \App\Actions\Contacts\CreateContactAction $action)
    {
        $action->execute($company, $request->validated());
        return redirect()->route('companies.show', $company)->with('success', 'Contact created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company, Contact $contact)
    {
        $this->authorize('update', $contact);
        return view('contacts.edit', compact('company', 'contact'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateContactRequest $request, Company $company, Contact $contact, \App\Actions\Contacts\UpdateContactAction $action)
    {
        $action->execute($contact, $request->validated());
        return redirect()->route('companies.show', $company)->with('success', 'Contact updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company, Contact $contact)
    {
        $this->authorize('delete', $contact);
        $contact->delete();
        
        event(new \App\Events\AuditableAction(
            $contact,
            'deleted',
            'contacts',
            $contact->toArray(),
            null
        ));

        return redirect()->route('companies.show', $company)->with('success', 'Contact deleted successfully.');
    }
}
