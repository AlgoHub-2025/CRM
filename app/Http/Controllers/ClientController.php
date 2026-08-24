<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ClientController extends Controller
{
    public function index()
    {
        Gate::authorize('viewAny', Client::class);
        return view('clients.index');
    }

    public function show(Client $client)
    {
        Gate::authorize('view', $client);
        $client->load(['company.contacts', 'primaryContact', 'opportunities', 'activities']);
        return view('clients.show', compact('client'));
    }

    public function create()
    {
        return redirect()->route('clients.index')->with('error', 'Clients cannot be created manually. They are automatically generated when an Opportunity is moved to Closed Won.');
    }
}
