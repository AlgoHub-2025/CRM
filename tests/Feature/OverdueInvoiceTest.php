<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Company;
use App\Models\Contact;
use App\Notifications\OverdueInvoiceReminderNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class OverdueInvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_reminders_and_updates_status()
    {
        Notification::fake();
        $company = Company::factory()->create();
        $contact = Contact::factory()->create(['company_id' => $company->id, 'email' => 'client@test.com']);
        $client = Client::factory()->create(['company_id' => $company->id, 'primary_contact_id' => $contact->id]);
        
        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
            'status' => 'sent',
            'due_date' => now()->subDays(2),
            'last_reminder_sent_at' => null
        ]);

        Artisan::call('app:check-overdue-invoices');

        Notification::assertSentOnDemand(
            OverdueInvoiceReminderNotification::class,
            function ($notification, $channels, $notifiable) use ($contact) {
                return $notifiable->routes['mail'] === 'client@test.com';
            }
        );

        $this->assertEquals('overdue', $invoice->fresh()->status);
        $this->assertNotNull($invoice->fresh()->last_reminder_sent_at);
    }

    public function test_it_respects_idempotency_window()
    {
        Notification::fake();
        $company = Company::factory()->create();
        $contact = Contact::factory()->create(['company_id' => $company->id, 'email' => 'client@test.com']);
        $client = Client::factory()->create(['company_id' => $company->id, 'primary_contact_id' => $contact->id]);
        
        // Reminder sent 10 hours ago -> should NOT send
        $invoice1 = Invoice::factory()->create([
            'client_id' => $client->id,
            'status' => 'overdue',
            'due_date' => now()->subDays(2),
            'last_reminder_sent_at' => now()->subHours(10)
        ]);

        // Reminder sent 24 hours ago -> SHOULD send
        $invoice2 = Invoice::factory()->create([
            'client_id' => $client->id,
            'status' => 'overdue',
            'due_date' => now()->subDays(2),
            'last_reminder_sent_at' => now()->subHours(24)
        ]);

        Artisan::call('app:check-overdue-invoices');

        // Should only be sent for invoice2
        Notification::assertSentOnDemand(
            OverdueInvoiceReminderNotification::class,
            function ($notification, $channels, $notifiable) use ($invoice2) {
                return $notification->invoice->id === $invoice2->id;
            }
        );
        
        // Ensure invoice1 wasn't triggered
        $this->assertEquals(now()->subHours(10)->toDateTimeString(), $invoice1->fresh()->last_reminder_sent_at->toDateTimeString());
        // Ensure invoice2 timestamp was updated
        $this->assertNotEquals(now()->subHours(24)->toDateTimeString(), $invoice2->fresh()->last_reminder_sent_at->toDateTimeString());
    }

    public function test_it_does_not_process_paid_invoices()
    {
        Notification::fake();
        $company = Company::factory()->create();
        $contact = Contact::factory()->create(['company_id' => $company->id, 'email' => 'client@test.com']);
        $client = Client::factory()->create(['company_id' => $company->id, 'primary_contact_id' => $contact->id]);
        
        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
            'status' => 'paid',
            'due_date' => now()->subDays(2),
            'last_reminder_sent_at' => null
        ]);

        Artisan::call('app:check-overdue-invoices');

        Notification::assertNothingSent();
        $this->assertNull($invoice->fresh()->last_reminder_sent_at);
    }
}
