<?php

namespace App\Actions\SupportTickets;

use App\Models\SupportTicket;

class ResolveTicketAction
{
    public function execute(SupportTicket $ticket): SupportTicket
    {
        $ticket->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);

        return $ticket;
    }
}
