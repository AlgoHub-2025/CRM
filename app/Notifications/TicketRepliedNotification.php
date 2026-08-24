<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use App\Models\TicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketRepliedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;
    public $message;

    public function __construct(SupportTicket $ticket, TicketMessage $message)
    {
        $this->ticket = $ticket;
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        // For Employees (User model), we send to both DB and mail
        if ($notifiable instanceof \App\Models\User) {
            return ['mail', 'database'];
        }
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Reply to Ticket: ' . $this->ticket->subject)
                    ->greeting('Hello!')
                    ->line('A new reply has been added to your support ticket.')
                    ->line('Message: ' . $this->message->message)
                    ->action('View Ticket', url('/tickets/' . $this->ticket->id));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'message' => 'New reply on ticket: ' . $this->ticket->subject,
            'url' => url('/tickets/' . $this->ticket->id)
        ];
    }
}
