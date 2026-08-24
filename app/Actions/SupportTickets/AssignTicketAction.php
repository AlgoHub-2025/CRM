<?php

namespace App\Actions\SupportTickets;

use App\Models\SupportTicket;
use App\Models\Employee;
use App\Notifications\TicketAssignedNotification;

use App\Events\AuditableAction;

class AssignTicketAction
{
    public function execute(SupportTicket $ticket, ?string $employeeId): SupportTicket
    {
        $oldValues = $ticket->only(['assigned_to']);

        $ticket->update(['assigned_to' => $employeeId]);

        if ($employeeId) {
            $employee = Employee::with('user')->find($employeeId);
            if ($employee && $employee->user) {
                $employee->user->notify(new TicketAssignedNotification($ticket));
            }
        }

        AuditableAction::dispatch(
            $ticket,
            'assigned',
            'SupportTickets',
            $oldValues,
            $ticket->only(['assigned_to'])
        );

        return $ticket;
    }
}
