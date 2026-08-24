<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', \App\Livewire\Dashboard\MainDashboard::class)->name('dashboard');
    Route::view('profile', 'profile')->name('profile');

    // HR & Staff
    Route::resource('employees', \App\Http\Controllers\EmployeeController::class)->except(['show']);

    // Leads & Opportunities (Module 3)
    Route::resource('leads', \App\Http\Controllers\LeadController::class);
    Route::post('leads/{lead}/convert', [\App\Http\Controllers\LeadConversionController::class, 'convert'])->name('leads.convert');
    Route::resource('opportunities', \App\Http\Controllers\OpportunityController::class)->only(['index']);
    Route::post('opportunities/{opportunity}/won', [\App\Http\Controllers\OpportunityController::class, 'markWon'])->name('opportunities.won');

    // CRM Core (Module 4)
    Route::resource('companies', \App\Http\Controllers\CompanyController::class);
    Route::resource('companies.contacts', \App\Http\Controllers\ContactController::class)->except(['index', 'show']);
    Route::resource('clients', \App\Http\Controllers\ClientController::class)->only(['index', 'show']);

    // Proposals (Module 5)
    Route::get('/proposals', \App\Livewire\Proposals\ProposalList::class)->name('proposals.index');
    Route::get('/proposals/create/{opportunity?}', \App\Livewire\Proposals\ProposalBuilder::class)->name('proposals.create');
    Route::get('/proposals/{proposal}/edit', \App\Livewire\Proposals\ProposalBuilder::class)->name('proposals.edit');
    Route::get('/proposals/{proposal}', [\App\Http\Controllers\ProposalController::class, 'show'])->name('proposals.show');
    Route::post('/proposals/{proposal}/accept', [\App\Http\Controllers\ProposalController::class, 'accept'])->name('proposals.accept');
    Route::post('/proposals/{proposal}/send', [\App\Http\Controllers\ProposalController::class, 'send'])->name('proposals.send');

    // Contracts
    Route::get('/contracts', \App\Livewire\Contracts\ContractList::class)->name('contracts.index');
    Route::get('/contracts/{contract}', [\App\Http\Controllers\ContractController::class, 'show'])->name('contracts.show');
    Route::post('/contracts/{contract}/activate', [\App\Http\Controllers\ContractController::class, 'activate'])->name('contracts.activate');

    // Projects (Module 6)
    Route::get('/projects', \App\Livewire\ProjectList::class)->name('projects.index');
    Route::get('/projects/{project}', \App\Livewire\ProjectDetail::class)->name('projects.show');
    Route::get('/projects/{project}/tasks', \App\Livewire\Projects\TaskBoard::class)->name('projects.tasks');

    // Invoices (Module 7)
    Route::get('/invoices', \App\Livewire\Invoices\InvoiceList::class)->name('invoices.index');
    Route::get('/invoices/create', \App\Livewire\Invoices\InvoiceBuilder::class)->name('invoices.create');
    Route::get('/invoices/{invoice}', \App\Livewire\Invoices\InvoiceDetail::class)->name('invoices.show');
    Route::get('/invoices/{invoice}/edit', \App\Livewire\Invoices\InvoiceBuilder::class)->name('invoices.edit');

    // Support Tickets (Module 8)
    Route::get('/tickets', \App\Livewire\Tickets\TicketList::class)->name('tickets.index');
    Route::get('/tickets/{ticket}', \App\Livewire\Tickets\TicketDetail::class)->name('tickets.show');
});

require __DIR__.'/auth.php';
