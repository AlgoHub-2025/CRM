<?php

namespace App\Actions\SupportTickets;

use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Support\Facades\Validator;

class ReplyToTicketAction
{
    public function execute(SupportTicket $ticket, array $data): TicketMessage
    {
        Validator::make($data, [
            'sender_type' => ['required', 'in:employee,client'],
            'sender_id' => ['required', 'string'],
            'logged_by_employee_id' => ['nullable', 'string', 'exists:employees,id'],
            'message' => ['required', 'string'],
        ])->validate();

        $message = $ticket->messages()->create([
            'sender_type' => $data['sender_type'],
            'sender_id' => $data['sender_id'],
            'logged_by_employee_id' => $data['logged_by_employee_id'] ?? null,
            'message' => $data['message'],
        ]);

        // Automatically update ticket status
        if ($data['sender_type'] === 'client') {
            // If the client replied, it's back in progress or open
            $ticket->update(['status' => 'open']);
        } else {
            // If an employee replied, we are waiting on the client
            $ticket->update(['status' => 'waiting_client']);
        }

        // Notify the relevant party
        if ($data['sender_type'] === 'client') {
            // Notify Assigned Employee or all users with update permission
            if ($ticket->assigned_to) {
                $employee = \App\Models\Employee::with('user')->find($ticket->assigned_to);
                if ($employee && $employee->user) {
                    $employee->user->notify(new \App\Notifications\TicketRepliedNotification($ticket, $message));
                }
            } else {
                // Find users with tickets.update.all permission
                $users = \App\Models\User::permission('tickets.update.all')->get();
                foreach ($users as $user) {
                    $user->notify(new \App\Notifications\TicketRepliedNotification($ticket, $message));
                }
            }
        } else {
            // Notify Client Primary Contact
            $client = $ticket->client;
            if ($client) {
                $email = null;
                if ($client->primary_contact_id && $client->primaryContact) {
                    $email = $client->primaryContact->email;
                }
                
                if (!$email) {
                    $decisionMaker = \App\Models\Contact::where('company_id', $client->company_id)->where('is_decision_maker', true)->first();
                    if ($decisionMaker) {
                        $email = $decisionMaker->email;
                    }
                }
                
                if (!$email) {
                    $anyContact = \App\Models\Contact::where('company_id', $client->company_id)->first();
                    if ($anyContact) {
                        $email = $anyContact->email;
                    }
                }

                if ($email) {
                    \Illuminate\Support\Facades\Notification::route('mail', $email)->notify(new \App\Notifications\TicketRepliedNotification($ticket, $message));
                }
            }
        }

        return $message;
    }
}
