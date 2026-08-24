<?php

namespace App\Livewire\Tickets;

use Livewire\Component;
use Livewire\Attributes\Layout;

class TicketDetail extends Component
{
    public $ticket;
    public $replyMessage = '';
    public $isProxyReply = false; // "Log Client Reply" checkbox
    
    public $status = 'open';
    public $assignee = '';

    public function mount(\App\Models\SupportTicket $ticket)
    {
        $this->authorize('view', $ticket);
        $this->ticket = $ticket;
        $this->status = $ticket->status;
        $this->assignee = $ticket->assigned_to;
    }
    
    public function submitReply(\App\Actions\SupportTickets\ReplyToTicketAction $action)
    {
        $this->authorize('update', $this->ticket);

        $this->validate([
            'replyMessage' => 'required|min:2',
            'isProxyReply' => 'boolean',
        ]);
        
        $employee = auth()->user()->employee;

        if ($this->isProxyReply) {
            // Logging on behalf of the client
            $action->execute($this->ticket, [
                'sender_type' => 'client',
                'sender_id' => $this->ticket->client_id,
                'logged_by_employee_id' => $employee->id,
                'message' => $this->replyMessage,
            ]);
        } else {
            // Regular employee reply
            $action->execute($this->ticket, [
                'sender_type' => 'employee',
                'sender_id' => $employee->id,
                'logged_by_employee_id' => null,
                'message' => $this->replyMessage,
            ]);
        }
        
        $this->reset(['replyMessage', 'isProxyReply']);
        $this->ticket->refresh();
        $this->status = $this->ticket->status;
    }

    public function updateStatus()
    {
        $this->authorize('update', $this->ticket);
        
        if ($this->status === 'resolved' && $this->ticket->status !== 'resolved') {
            app(\App\Actions\SupportTickets\ResolveTicketAction::class)->execute($this->ticket);
        } else {
            $this->ticket->update(['status' => $this->status]);
        }
        
        $this->ticket->refresh();
    }

    public function updateAssignee(\App\Actions\SupportTickets\AssignTicketAction $action)
    {
        $this->authorize('update', $this->ticket);
        $action->execute($this->ticket, $this->assignee ?: null);
        $this->ticket->refresh();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.tickets.ticket-detail', [
            'messages' => $this->ticket->messages()->with(['sender'])->oldest()->get(),
            'employees' => \App\Models\Employee::with('user')->get(),
        ]);
    }
}
