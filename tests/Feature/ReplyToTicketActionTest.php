<?php

namespace Tests\Feature;

use App\Actions\SupportTickets\ReplyToTicketAction;
use App\Models\Client;
use App\Models\Employee;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReplyToTicketActionTest extends TestCase
{
    // use RefreshDatabase;

    public function test_client_reply_sets_status_to_open()
    {
        $ticket = SupportTicket::factory()->create(['status' => 'waiting_client']);
        $client = Client::factory()->create();

        $action = new ReplyToTicketAction();
        try {
            $message = $action->execute($ticket, [
                'sender_type' => 'client',
                'sender_id' => $client->id,
                'message' => 'Hello',
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        $this->assertEquals('open', $ticket->fresh()->status);
        $this->assertEquals('client', $message->sender_type);
    }

    public function test_employee_reply_sets_status_to_waiting_client()
    {
        $ticket = SupportTicket::factory()->create(['status' => 'open']);
        $employee = Employee::factory()->create();

        $action = new ReplyToTicketAction();
        $message = $action->execute($ticket, [
            'sender_type' => 'employee',
            'sender_id' => $employee->id,
            'message' => 'Hello',
        ]);

        $this->assertEquals('waiting_client', $ticket->fresh()->status);
        $this->assertEquals('employee', $message->sender_type);
    }
}
