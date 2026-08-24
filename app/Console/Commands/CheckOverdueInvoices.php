<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\Contact;
use App\Notifications\OverdueInvoiceReminderNotification;
use Illuminate\Support\Facades\Notification;

class CheckOverdueInvoices extends Command
{
    protected $signature = 'app:check-overdue-invoices';
    protected $description = 'Check for overdue invoices and send reminders';

    public function handle()
    {
        $invoices = Invoice::whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->where('due_date', '<', now()->startOfDay())
            ->where(function ($query) {
                $query->whereNull('last_reminder_sent_at')
                      ->orWhere('last_reminder_sent_at', '<', now()->subHours(23));
            })
            ->get();

        $count = 0;
        foreach ($invoices as $invoice) {
            // Update status if it's not already overdue
            if ($invoice->status !== 'overdue') {
                $invoice->status = 'overdue';
            }
            
            // Stamp the reminder time synchronously to ensure idempotency
            $invoice->last_reminder_sent_at = now();
            $invoice->save();

            // Resolve email
            $email = null;
            $client = $invoice->client;
            
            if ($client) {
                if ($client->primary_contact_id && $client->primaryContact) {
                    $email = $client->primaryContact->email;
                }
                
                if (!$email) {
                    $decisionMaker = Contact::where('company_id', $client->company_id)->where('is_decision_maker', true)->first();
                    if ($decisionMaker) {
                        $email = $decisionMaker->email;
                    }
                }
                
                if (!$email) {
                    $anyContact = Contact::where('company_id', $client->company_id)->first();
                    if ($anyContact) {
                        $email = $anyContact->email;
                    }
                }
            }

            if ($email) {
                Notification::route('mail', $email)->notify(new OverdueInvoiceReminderNotification($invoice));
                $count++;
            } else {
                $this->warn("No email found for Invoice {$invoice->invoice_number}");
            }
        }

        $this->info("Processed {$invoices->count()} invoices, sent {$count} reminders.");
    }
}
