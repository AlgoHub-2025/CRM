<?php

namespace App\Notifications;

use App\Models\Proposal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProposalSentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $proposal;

    /**
     * Create a new notification instance.
     */
    public function __construct(Proposal $proposal)
    {
        $this->proposal = $proposal;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Proposal: ' . $this->proposal->project_title)
                    ->greeting('Hello!')
                    ->line('We have prepared a new proposal for your project: ' . $this->proposal->project_title)
                    ->line('Total Amount: $' . number_format($this->proposal->total, 2))
                    ->action('View Proposal', url('/proposals/' . $this->proposal->id))
                    ->line('Thank you for your business!');
    }
}
