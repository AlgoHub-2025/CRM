<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OverdueInvoiceReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('OVERDUE: Invoice ' . $this->invoice->invoice_number)
                    ->greeting('Hello!')
                    ->line('This is a reminder that your invoice ' . $this->invoice->invoice_number . ' is currently overdue.')
                    ->line('Due Date: ' . $this->invoice->due_date->format('M d, Y'))
                    ->line('Amount Due: $' . number_format($this->invoice->total - $this->invoice->paid_amount, 2))
                    ->action('View Invoice', url('/invoices/' . $this->invoice->id))
                    ->line('Please arrange for payment as soon as possible. Thank you.');
    }
}
