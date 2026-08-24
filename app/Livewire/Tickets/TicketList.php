<?php

namespace App\Livewire\Tickets;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Actions\SupportTickets\CreateSupportTicketAction;

class TicketList extends Component
{
    use WithPagination;

    public $status = '';
    public $priority = '';
    public $search = '';

    // Ticket creation form
    public $showCreateForm = false;
    public $ticketClientId = '';
    public $ticketProjectId = '';
    public $ticketSubject = '';
    public $ticketDescription = '';
    public $ticketPriority = 'medium';

    public function updating($property)
    {
        if (in_array($property, ['status', 'priority', 'search'])) {
            $this->resetPage();
        }
    }

    public function toggleCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
        $this->reset(['ticketClientId', 'ticketProjectId', 'ticketSubject', 'ticketDescription', 'ticketPriority']);
    }

    public function createTicket(CreateSupportTicketAction $action)
    {
        $ticket = $action->execute([
            'client_id' => $this->ticketClientId,
            'project_id' => $this->ticketProjectId ?: null,
            'subject' => $this->ticketSubject,
            'description' => $this->ticketDescription,
            'priority' => $this->ticketPriority,
        ]);

        $this->reset(['ticketClientId', 'ticketProjectId', 'ticketSubject', 'ticketDescription', 'ticketPriority', 'showCreateForm']);

        return redirect()->route('tickets.show', $ticket);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $query = \App\Models\SupportTicket::with(['client.company', 'project', 'assignee'])->latest();

        if ($this->status) {
            $query->where('status', $this->status);
        }
        if ($this->priority) {
            $query->where('priority', $this->priority);
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('subject', 'ilike', '%' . $this->search . '%')
                  ->orWhere('description', 'ilike', '%' . $this->search . '%');
            });
        }

        // Apply policy scope
        $user = auth()->user();
        if (!$user->can('tickets.view.global')) {
            $employeeId = $user->employee->id ?? null;
            $query->where(function ($q) use ($employeeId) {
                $q->where('assigned_to', $employeeId)
                  ->orWhereHas('project', function ($q2) use ($employeeId) {
                      $q2->where('project_manager_id', $employeeId);
                  })
                  ->orWhereHas('client.company', function ($q3) use ($employeeId) {
                      $q3->where('account_manager_id', $employeeId);
                  });
            });
        }

        return view('livewire.tickets.ticket-list', [
            'tickets' => $query->paginate(10),
            'clients' => \App\Models\Client::with('company')->get(),
            'projects' => \App\Models\Project::orderBy('name')->get(['id', 'name']),
        ]);
    }
}

