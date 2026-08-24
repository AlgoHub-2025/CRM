<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Client;
use App\Models\Proposal;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\SupportTicket;
use App\Models\Opportunity;
use App\Models\AuditLog;
use App\Events\AuditableAction;
use Illuminate\Support\Facades\Event;

class AuditRetrofitTest extends TestCase
{
    use RefreshDatabase;

    public function test_accept_proposal_dispatches_audit_log()
    {
        Event::fake([AuditableAction::class]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $company = Company::factory()->create();
        $client = Client::factory()->create(['company_id' => $company->id]);
        $lead = \App\Models\Lead::factory()->create(['company_id' => $company->id]);
        $opportunity = Opportunity::factory()->create(['lead_id' => $lead->id]);
        $proposal = Proposal::factory()->create([
            'client_id' => $client->id,
            'opportunity_id' => $opportunity->id,
            'status' => 'sent'
        ]);

        $action = app(\App\Actions\Proposals\AcceptProposalAction::class);
        $action->execute($proposal);

        Event::assertDispatched(AuditableAction::class, function ($event) use ($proposal) {
            return $event->action === 'accepted'
                && $event->module === 'Proposals'
                && $event->model->id === $proposal->id
                && $event->oldValues['status'] === 'sent'
                && $event->newValues['status'] === 'accepted';
        });
    }

    public function test_activate_contract_dispatches_audit_log()
    {
        Event::fake([AuditableAction::class]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $company = Company::factory()->create();
        $client = Client::factory()->create(['company_id' => $company->id]);
        $lead = \App\Models\Lead::factory()->create(['company_id' => $company->id]);
        $opportunity = Opportunity::factory()->create(['lead_id' => $lead->id]);
        $proposal = Proposal::factory()->create(['client_id' => $client->id, 'opportunity_id' => $opportunity->id, 'status' => 'accepted']);
        $contract = Contract::factory()->create([
            'client_id' => $client->id,
            'proposal_id' => $proposal->id,
            'status' => 'draft'
        ]);

        $action = app(\App\Actions\Contracts\ActivateContractAction::class);
        $action->execute($contract);

        Event::assertDispatched(AuditableAction::class, function ($event) use ($contract) {
            return $event->action === 'activated'
                && $event->module === 'Contracts'
                && $event->model->id === $contract->id
                && $event->oldValues['status'] === 'draft'
                && $event->newValues['status'] === 'active';
        });
    }

    public function test_assign_ticket_dispatches_audit_log()
    {
        Event::fake([AuditableAction::class]);
        \Illuminate\Support\Facades\Notification::fake();

        $user = User::factory()->create();
        $employee = \App\Models\Employee::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $ticket = SupportTicket::factory()->create(['assigned_to' => null]);

        $action = app(\App\Actions\SupportTickets\AssignTicketAction::class);
        $action->execute($ticket, $employee->id);

        Event::assertDispatched(AuditableAction::class, function ($event) use ($ticket, $employee) {
            return $event->action === 'assigned'
                && $event->module === 'SupportTickets'
                && $event->model->id === $ticket->id
                && $event->oldValues['assigned_to'] === null
                && $event->newValues['assigned_to'] === $employee->id;
        });
    }

    public function test_record_payment_dispatches_audit_log()
    {
        Event::fake([AuditableAction::class]);

        $user = User::factory()->create();
        $employee = \App\Models\Employee::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $invoice = Invoice::factory()->create([
            'status' => 'sent',
            'total' => 1000,
            'paid_amount' => 0
        ]);

        $action = app(\App\Actions\Payments\RecordPaymentAction::class);
        $action->execute($invoice, [
            'amount' => 500,
            'method' => 'bank_transfer',
            'transaction_reference' => 'TXN123'
        ]);

        Event::assertDispatched(AuditableAction::class, function ($event) use ($invoice) {
            return $event->action === 'payment_recorded'
                && $event->module === 'Invoices'
                && $event->model->id === $invoice->id
                && $event->oldValues['paid_amount'] == 0
                && $event->newValues['paid_amount'] == 500;
        });
    }

    public function test_mark_opportunity_won_dispatches_audit_log_for_new_client()
    {
        Event::fake([AuditableAction::class]);

        $user = User::factory()->create();
        $this->actingAs($user);

        $company = Company::factory()->create();
        $lead = \App\Models\Lead::factory()->create(['company_id' => $company->id]);
        $opportunity = Opportunity::factory()->create([
            'lead_id' => $lead->id
        ]);
        
        $wonStage = \App\Models\PipelineStage::factory()->create(['is_won' => true]);

        $action = app(\App\Actions\Opportunities\MarkOpportunityWonAction::class);
        $action->execute($opportunity, $wonStage->id);

        Event::assertDispatched(AuditableAction::class, function ($event) use ($opportunity) {
            return $event->action === 'marked_won'
                && $event->module === 'Opportunities'
                && $event->model->id === $opportunity->id
                && isset($event->newValues['client_action'])
                && $event->newValues['client_action'] === 'created';
        });
    }
}
