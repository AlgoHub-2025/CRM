<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Tickets\TicketDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Models\SupportTicket;
use App\Models\TicketMessage;
use App\Models\User;
use App\Models\Employee;
use Spatie\Permission\Models\Permission;

class TicketDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_real_ticket_messages()
    {
        $user = User::factory()->create();
        Permission::firstOrCreate(['name' => 'tickets.view.global']);
        $user->givePermissionTo('tickets.view.global');
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $ticket = SupportTicket::factory()->create([
            'subject' => 'Real Dynamic Subject 123'
        ]);

        $message = TicketMessage::factory()->create([
            'ticket_id' => $ticket->id,
            'message' => 'Real Dynamic Message Body 456',
            'sender_type' => 'employee',
            'sender_id' => $employee->id,
        ]);

        $this->actingAs($user);

        Livewire::test(TicketDetail::class, ['ticket' => $ticket])
            ->assertSee('Real Dynamic Subject 123')
            ->assertSee('Real Dynamic Message Body 456')
            ->assertDontSee('Cannot access billing portal')
            ->assertDontSee('Hi team, I am trying to download my latest invoice');
    }
}
