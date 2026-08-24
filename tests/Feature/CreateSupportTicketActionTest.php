<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Employee;
use App\Models\Client;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Actions\SupportTickets\CreateSupportTicketAction;

class CreateSupportTicketActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_ticket_with_initial_message()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->assignRole('Support');
        $this->actingAs($user);

        $client = Client::factory()->create();

        $action = new CreateSupportTicketAction();
        $ticket = $action->execute([
            'client_id' => $client->id,
            'subject' => 'Login issue',
            'description' => 'Client cannot log in to the dashboard.',
            'priority' => 'high',
        ]);

        $this->assertInstanceOf(SupportTicket::class, $ticket);
        $this->assertEquals('open', $ticket->status);

        // The first message should exist
        $messages = TicketMessage::where('ticket_id', $ticket->id)->get();
        $this->assertCount(1, $messages);

        $firstMessage = $messages->first();
        $this->assertEquals('client', $firstMessage->sender_type);
        $this->assertEquals($client->id, $firstMessage->sender_id);
        $this->assertEquals('Client cannot log in to the dashboard.', $firstMessage->message);
    }

    public function test_first_message_has_logged_by_employee_id()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $user->assignRole('Support');
        $this->actingAs($user);

        $client = Client::factory()->create();

        $action = new CreateSupportTicketAction();
        $ticket = $action->execute([
            'client_id' => $client->id,
            'subject' => 'Billing question',
            'description' => 'Client wants to know their balance.',
            'priority' => 'medium',
        ]);

        $firstMessage = TicketMessage::where('ticket_id', $ticket->id)->first();

        // The audit trail must record which employee transcribed the message
        $this->assertEquals($employee->id, $firstMessage->logged_by_employee_id);
    }

    public function test_ticket_without_authenticated_user_has_null_logged_by()
    {
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $client = Client::factory()->create();

        // No actingAs — simulates a system creation, which is now explicitly banned without auth
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Ticket creation proxy failed: No valid employee ID found');

        $action = new CreateSupportTicketAction();
        $action->execute([
            'client_id' => $client->id,
            'subject' => 'System-generated ticket',
            'description' => 'Auto-created from monitoring.',
            'priority' => 'low',
        ]);
    }
}
