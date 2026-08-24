<?php

namespace App\Actions\SupportTickets;

use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CreateSupportTicketAction
{
    /**
     * Create a support ticket and its initial message in a single transaction.
     * The first message represents the client's original request, proxied by the logged-in employee.
     */
    public function execute(array $data): SupportTicket
    {
        Validator::make($data, [
            'client_id' => ['required', 'string', 'exists:clients,id'],
            'project_id' => ['nullable', 'string', 'exists:projects,id'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['required', 'in:low,medium,high,critical'],
            'assigned_to' => ['nullable', 'string', 'exists:employees,id'],
        ])->validate();

        return DB::transaction(function () use ($data) {
            $ticket = SupportTicket::create([
                'client_id' => $data['client_id'],
                'project_id' => $data['project_id'] ?? null,
                'subject' => $data['subject'],
                'description' => $data['description'],
                'priority' => $data['priority'] ?? 'medium',
                'status' => 'open',
                'assigned_to' => $data['assigned_to'] ?? null,
            ]);

            // Create the first message representing the client's original request
            $loggedByEmployeeId = auth()->user()?->employee?->id;
            
            if (!$loggedByEmployeeId) {
                throw new \Exception('Ticket creation proxy failed: No valid employee ID found for the current user to attribute the audit trail.');
            }

            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'sender_type' => 'client',
                'sender_id' => $data['client_id'],
                'logged_by_employee_id' => $loggedByEmployeeId,
                'message' => $data['description'],
            ]);

            return $ticket;
        });
    }
}

