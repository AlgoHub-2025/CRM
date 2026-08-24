<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\SupportTicket;
use App\Models\Client;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Employee;
use App\Models\User;
use App\Actions\SupportTickets\ReplyToTicketAction;
use App\Actions\SupportTickets\AssignTicketAction;
use App\Notifications\TicketRepliedNotification;
use App\Notifications\TicketAssignedNotification;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;

class TicketNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_reply_notifies_client_primary_contact()
    {
        Notification::fake();
        $company = Company::factory()->create();
        $contact = Contact::factory()->create(['company_id' => $company->id, 'email' => 'client@test.com']);
        $client = Client::factory()->create(['company_id' => $company->id, 'primary_contact_id' => $contact->id]);
        $ticket = SupportTicket::factory()->create(['client_id' => $client->id]);
        $employee = Employee::factory()->create();

        app(ReplyToTicketAction::class)->execute($ticket, [
            'sender_type' => 'employee',
            'sender_id' => $employee->id,
            'message' => 'Hello Client'
        ]);

        Notification::assertSentOnDemand(
            TicketRepliedNotification::class,
            function ($notification, $channels, $notifiable) use ($contact) {
                return $notifiable->routes['mail'] === 'client@test.com';
            }
        );
        $this->assertEquals('waiting_client', $ticket->fresh()->status);
    }

    public function test_client_reply_notifies_assigned_employee()
    {
        Notification::fake();
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $ticket = SupportTicket::factory()->create(['assigned_to' => $employee->id]);
        $client = Client::factory()->create();

        app(ReplyToTicketAction::class)->execute($ticket, [
            'sender_type' => 'client',
            'sender_id' => $client->id,
            'message' => 'Hello Employee'
        ]);

        Notification::assertSentTo(
            $user,
            TicketRepliedNotification::class
        );
        $this->assertEquals('open', $ticket->fresh()->status);
    }

    public function test_client_reply_notifies_all_users_with_update_permission_if_unassigned()
    {
        Notification::fake();
        Permission::findOrCreate('tickets.update.all');
        $user1 = User::factory()->create();
        $user1->givePermissionTo('tickets.update.all');
        $user2 = User::factory()->create();
        $user2->givePermissionTo('tickets.update.all');
        $user3 = User::factory()->create(); // no permission

        $ticket = SupportTicket::factory()->create(['assigned_to' => null]);
        $client = Client::factory()->create();

        app(ReplyToTicketAction::class)->execute($ticket, [
            'sender_type' => 'client',
            'sender_id' => $client->id,
            'message' => 'Help me'
        ]);

        Notification::assertSentTo([$user1, $user2], TicketRepliedNotification::class);
        Notification::assertNotSentTo([$user3], TicketRepliedNotification::class);
    }

    public function test_assigning_ticket_notifies_assigned_employee()
    {
        Notification::fake();
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $ticket = SupportTicket::factory()->create(['assigned_to' => null]);

        app(AssignTicketAction::class)->execute($ticket, $employee->id);

        Notification::assertSentTo(
            $user,
            TicketAssignedNotification::class
        );
        $this->assertEquals($employee->id, $ticket->fresh()->assigned_to);
    }
}
