<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;

    public function __construct(SupportTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Ticket Assigned To You: ' . $this->ticket->subject)
                    ->greeting('Hello!')
                    ->line('A support ticket has been assigned to you.')
                    ->line('Subject: ' . $this->ticket->subject)
                    ->action('View Ticket', url('/tickets/' . $this->ticket->id));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'message' => 'Ticket assigned to you: ' . $this->ticket->subject,
            'url' => url('/tickets/' . $this->ticket->id)
        ];
    }
}
