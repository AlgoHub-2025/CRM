import os
import datetime

models_dir = 'app/Models'
migrations_dir = 'database/migrations'

# Ensure directories exist
os.makedirs(models_dir, exist_ok=True)
os.makedirs(migrations_dir, exist_ok=True)

start_time = datetime.datetime.now()

tables = [
    # 1. employees
    {
        "model": "Employee",
        "table": "employees",
        "soft_delete": True,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('user_id')->nullable();
            $table->string('employee_code')->unique();
            $table->string('designation');
            $table->string('department');
            $table->string('phone')->nullable();
            $table->date('hire_date')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            
            $table->index('status');
            $table->index('department');
            $table->index('created_at');"""
    },
    # 2. companies
    {
        "model": "Company",
        "table": "companies",
        "soft_delete": True,
        "schema": """$table->ulid('id')->primary();
            $table->string('name');
            $table->string('industry')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('tax_number')->nullable();
            $table->ulid('account_manager_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_manager_id')->references('id')->on('employees')->nullOnDelete();
            
            $table->index('account_manager_id');
            $table->index('created_at');"""
    },
    # 3. contacts
    {
        "model": "Contact",
        "table": "contacts",
        "soft_delete": True,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('company_id');
            $table->string('name');
            $table->string('designation')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->boolean('is_decision_maker')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
            
            $table->index('company_id');
            $table->index('created_at');"""
    },
    # 4. lead_sources
    {
        "model": "LeadSource",
        "table": "lead_sources",
        "soft_delete": False,
        "schema": """$table->ulid('id')->primary();
            $table->string('name');
            $table->timestamps();"""
    },
    # 5. pipeline_stages
    {
        "model": "PipelineStage",
        "table": "pipeline_stages",
        "soft_delete": False,
        "schema": """$table->ulid('id')->primary();
            $table->string('name');
            $table->integer('order')->default(0);
            $table->enum('type', ['lead', 'opportunity']);
            $table->timestamps();
            
            $table->index('type');
            $table->index('order');"""
    },
    # 6. leads
    {
        "model": "Lead",
        "table": "leads",
        "soft_delete": True,
        "schema": """$table->ulid('id')->primary();
            $table->string('name');
            $table->ulid('company_id')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('website')->nullable();
            $table->string('location')->nullable();
            $table->string('industry')->nullable();
            $table->ulid('source_id')->nullable();
            $table->string('interested_service')->nullable();
            $table->bigInteger('estimated_budget')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->ulid('assigned_to')->nullable();
            $table->ulid('status_id');
            $table->text('description')->nullable();
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            $table->foreign('source_id')->references('id')->on('lead_sources')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('status_id')->references('id')->on('pipeline_stages')->restrictOnDelete();
            
            $table->index('company_id');
            $table->index('assigned_to');
            $table->index('status_id');
            $table->index('priority');
            $table->index('created_at');"""
    },
    # 7. opportunities
    {
        "model": "Opportunity",
        "table": "opportunities",
        "soft_delete": True,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('lead_id')->nullable();
            $table->ulid('client_id')->nullable();
            $table->string('title');
            $table->string('service')->nullable();
            $table->bigInteger('value')->default(0);
            $table->unsignedTinyInteger('probability')->default(0);
            $table->date('expected_close_date')->nullable();
            $table->ulid('assigned_to')->nullable();
            $table->ulid('stage_id');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lead_id')->references('id')->on('leads')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('stage_id')->references('id')->on('pipeline_stages')->restrictOnDelete();
            
            $table->index('lead_id');
            $table->index('client_id');
            $table->index('assigned_to');
            $table->index('stage_id');
            $table->index('expected_close_date');
            $table->index('created_at');"""
    },
    # 8. activities
    {
        "model": "Activity",
        "table": "activities",
        "soft_delete": False,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('subject_id');
            $table->string('subject_type');
            $table->ulid('employee_id')->nullable();
            $table->enum('type', ['call', 'whatsapp', 'email', 'meeting', 'video', 'sms', 'note']);
            $table->timestamp('occurred_at');
            $table->string('subject_line')->nullable();
            $table->text('description')->nullable();
            $table->text('result')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->nullOnDelete();
            
            $table->index(['subject_type', 'subject_id']);
            $table->index('employee_id');
            $table->index('occurred_at');
            $table->index('type');"""
    },
    # 9. follow_ups
    {
        "model": "FollowUp",
        "table": "follow_ups",
        "soft_delete": False,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('activity_id')->nullable();
            $table->ulid('subject_id');
            $table->string('subject_type');
            $table->ulid('employee_id')->nullable();
            $table->timestamp('due_at');
            $table->enum('status', ['pending', 'done', 'overdue'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('activity_id')->references('id')->on('activities')->nullOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->nullOnDelete();
            
            $table->index(['subject_type', 'subject_id']);
            $table->index('employee_id');
            $table->index('status');
            $table->index('due_at');"""
    },
    # 10. clients
    {
        "model": "Client",
        "table": "clients",
        "soft_delete": True,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('company_id');
            $table->ulid('primary_contact_id')->nullable();
            $table->ulid('converted_from_opportunity_id')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->restrictOnDelete();
            $table->foreign('primary_contact_id')->references('id')->on('contacts')->nullOnDelete();
            $table->foreign('converted_from_opportunity_id')->references('id')->on('opportunities')->nullOnDelete();
            
            $table->index('company_id');
            $table->index('status');
            $table->index('created_at');
        });
        
        Schema::table('opportunities', function (Blueprint $table) {
            $table->foreign('client_id')->references('id')->on('clients')->nullOnDelete();
        """,
        "down_schema": """Schema::table('opportunities', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
        });
        Schema::dropIfExists('clients');"""
    },
    # 11. proposals
    {
        "model": "Proposal",
        "table": "proposals",
        "soft_delete": True,
        "schema": """$table->ulid('id')->primary();
            $table->string('proposal_number')->unique();
            $table->ulid('client_id');
            $table->string('project_title');
            $table->enum('status', ['draft', 'sent', 'viewed', 'negotiation', 'accepted', 'rejected', 'expired'])->default('draft');
            $table->bigInteger('subtotal')->default(0);
            $table->bigInteger('discount')->default(0);
            $table->bigInteger('tax')->default(0);
            $table->bigInteger('total')->default(0);
            $table->string('currency')->default('USD');
            $table->date('valid_until')->nullable();
            $table->text('payment_terms')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients')->restrictOnDelete();
            
            $table->index('client_id');
            $table->index('status');
            $table->index('valid_until');
            $table->index('created_at');"""
    },
    # 12. proposal_items
    {
        "model": "ProposalItem",
        "table": "proposal_items",
        "soft_delete": False,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('proposal_id');
            $table->text('description');
            $table->integer('quantity')->default(1);
            $table->bigInteger('unit_price')->default(0);
            $table->bigInteger('line_total')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('proposal_id')->references('id')->on('proposals')->cascadeOnDelete();
            
            $table->index('proposal_id');"""
    },
    # 13. contracts
    {
        "model": "Contract",
        "table": "contracts",
        "soft_delete": True,
        "schema": """$table->ulid('id')->primary();
            $table->string('contract_number')->unique();
            $table->ulid('client_id');
            $table->ulid('proposal_id')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->bigInteger('value')->default(0);
            $table->text('payment_terms')->nullable();
            $table->text('scope')->nullable();
            $table->enum('status', ['draft', 'active', 'completed', 'terminated'])->default('draft');
            $table->string('signed_document_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients')->restrictOnDelete();
            $table->foreign('proposal_id')->references('id')->on('proposals')->nullOnDelete();
            
            $table->index('client_id');
            $table->index('proposal_id');
            $table->index('status');
            $table->index('end_date');
            $table->index('created_at');"""
    },
    # 14. projects
    {
        "model": "Project",
        "table": "projects",
        "soft_delete": True,
        "schema": """$table->ulid('id')->primary();
            $table->string('name');
            $table->ulid('client_id');
            $table->ulid('contract_id')->nullable();
            $table->ulid('project_manager_id')->nullable();
            $table->date('start_date')->nullable();
            $table->date('deadline')->nullable();
            $table->bigInteger('budget')->default(0);
            $table->string('technology')->nullable();
            $table->enum('status', ['not_started', 'planning', 'development', 'testing', 'client_review', 'revision', 'completed', 'maintenance', 'cancelled'])->default('not_started');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients')->restrictOnDelete();
            $table->foreign('contract_id')->references('id')->on('contracts')->nullOnDelete();
            $table->foreign('project_manager_id')->references('id')->on('employees')->nullOnDelete();
            
            $table->index('client_id');
            $table->index('contract_id');
            $table->index('project_manager_id');
            $table->index('status');
            $table->index('deadline');
            $table->index('created_at');"""
    },
    # 15. project_members
    {
        "model": "ProjectMember",
        "table": "project_members",
        "soft_delete": False,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('project_id');
            $table->ulid('employee_id');
            $table->string('role')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
            
            $table->index('project_id');
            $table->index('employee_id');"""
    },
    # 16. milestones
    {
        "model": "Milestone",
        "table": "milestones",
        "soft_delete": False,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('project_id');
            $table->string('name');
            $table->date('due_date')->nullable();
            $table->integer('order')->default(0);
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            
            $table->index('project_id');
            $table->index('due_date');
            $table->index('status');"""
    },
    # 17. tasks
    {
        "model": "Task",
        "table": "tasks",
        "soft_delete": False,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('project_id');
            $table->ulid('milestone_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->ulid('assigned_to')->nullable();
            $table->string('priority')->default('medium');
            $table->date('deadline')->nullable();
            $table->enum('status', ['todo', 'in_progress', 'review', 'completed', 'blocked'])->default('todo');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('milestone_id')->references('id')->on('milestones')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('employees')->nullOnDelete();
            
            $table->index('project_id');
            $table->index('milestone_id');
            $table->index('assigned_to');
            $table->index('status');
            $table->index('deadline');
            $table->index('created_at');"""
    },
    # 18. task_comments
    {
        "model": "TaskComment",
        "table": "task_comments",
        "soft_delete": False,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('task_id');
            $table->ulid('employee_id')->nullable();
            $table->text('comment');
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('tasks')->cascadeOnDelete();
            $table->foreign('employee_id')->references('id')->on('employees')->nullOnDelete();
            
            $table->index('task_id');
            $table->index('employee_id');
            $table->index('created_at');"""
    },
    # 19. invoices
    {
        "model": "Invoice",
        "table": "invoices",
        "soft_delete": True,
        "schema": """$table->ulid('id')->primary();
            $table->string('invoice_number')->unique();
            $table->ulid('client_id');
            $table->ulid('project_id')->nullable();
            $table->date('issue_date');
            $table->date('due_date');
            $table->bigInteger('subtotal')->default(0);
            $table->bigInteger('discount')->default(0);
            $table->bigInteger('tax')->default(0);
            $table->bigInteger('total')->default(0);
            $table->bigInteger('paid_amount')->default(0);
            $table->enum('status', ['draft', 'sent', 'partially_paid', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients')->restrictOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
            
            $table->index('client_id');
            $table->index('project_id');
            $table->index('status');
            $table->index('due_date');
            $table->index('created_at');"""
    },
    # 20. invoice_items
    {
        "model": "InvoiceItem",
        "table": "invoice_items",
        "soft_delete": False,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('invoice_id');
            $table->text('description');
            $table->integer('quantity')->default(1);
            $table->bigInteger('unit_price')->default(0);
            $table->bigInteger('line_total')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->cascadeOnDelete();
            
            $table->index('invoice_id');"""
    },
    # 21. payments
    {
        "model": "Payment",
        "table": "payments",
        "soft_delete": False,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('invoice_id');
            $table->ulid('client_id');
            $table->bigInteger('amount');
            $table->enum('method', ['bank_transfer', 'cash', 'cheque', 'online', 'other']);
            $table->string('transaction_reference')->nullable();
            $table->timestamp('paid_at');
            $table->ulid('received_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->restrictOnDelete();
            $table->foreign('client_id')->references('id')->on('clients')->restrictOnDelete();
            $table->foreign('received_by')->references('id')->on('employees')->nullOnDelete();
            
            $table->index('invoice_id');
            $table->index('client_id');
            $table->index('received_by');
            $table->index('paid_at');
            $table->index('created_at');"""
    },
    # 22. support_tickets
    {
        "model": "SupportTicket",
        "table": "support_tickets",
        "soft_delete": True,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('client_id');
            $table->ulid('project_id')->nullable();
            $table->string('subject');
            $table->text('description');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['open', 'assigned', 'in_progress', 'waiting_client', 'resolved', 'closed'])->default('open');
            $table->ulid('assigned_to')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('client_id')->references('id')->on('clients')->restrictOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('employees')->nullOnDelete();
            
            $table->index('client_id');
            $table->index('project_id');
            $table->index('assigned_to');
            $table->index('status');
            $table->index('priority');
            $table->index('created_at');"""
    },
    # 23. ticket_messages
    {
        "model": "TicketMessage",
        "table": "ticket_messages",
        "soft_delete": False,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('ticket_id');
            $table->enum('sender_type', ['employee', 'client']);
            $table->ulid('sender_id');
            $table->text('message');
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('support_tickets')->cascadeOnDelete();
            
            $table->index('ticket_id');
            $table->index(['sender_type', 'sender_id']);
            $table->index('created_at');"""
    },
    # 24. documents
    {
        "model": "Document",
        "table": "documents",
        "soft_delete": False,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('documentable_id');
            $table->string('documentable_type');
            $table->string('name');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->ulid('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('uploaded_by')->references('id')->on('employees')->nullOnDelete();
            
            $table->index(['documentable_type', 'documentable_id']);
            $table->index('uploaded_by');
            $table->index('created_at');"""
    },
    # 25. audit_logs
    {
        "model": "AuditLog",
        "table": "audit_logs",
        "soft_delete": False,
        "schema": """$table->ulid('id')->primary();
            $table->ulid('user_id')->nullable();
            $table->string('action');
            $table->string('module');
            $table->string('record_type');
            $table->ulid('record_id');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            
            $table->index('user_id');
            $table->index(['record_type', 'record_id']);
            $table->index('module');
            $table->index('created_at');"""
    },
    # 26. settings
    {
        "model": "Setting",
        "table": "settings",
        "soft_delete": False,
        "schema": """$table->ulid('id')->primary();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();"""
    }
]

migration_stub = """<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{{
    /**
     * Run the migrations.
     */
    public function up(): void
    {{
        Schema::create('{table}', function (Blueprint $table) {{
            {schema}
        }});
    }}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {{
        {down_schema}
    }}
}};
"""

model_stub = """<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
{soft_delete_use}

class {model} extends Model
{{
    use HasUlids{soft_delete_trait};

    protected $guarded = [];
}}
"""

for i, table_def in enumerate(tables):
    # Model
    model_name = table_def['model']
    table_name = table_def['table']
    soft_delete = table_def['soft_delete']
    
    use_stmt = "use Illuminate\\Database\\Eloquent\\SoftDeletes;" if soft_delete else ""
    trait_stmt = ", SoftDeletes" if soft_delete else ""
    
    model_content = model_stub.format(model=model_name, soft_delete_use=use_stmt, soft_delete_trait=trait_stmt)
    
    with open(f"{models_dir}/{model_name}.php", "w") as f:
        f.write(model_content)

    # Migration
    timestamp = (start_time + datetime.timedelta(seconds=i+10)).strftime('%Y_%m_%d_%H%M%S')
    migration_file = f"{migrations_dir}/{timestamp}_create_{table_name}_table.php"
    
    down_schema = table_def.get("down_schema", f"Schema::dropIfExists('{table_name}');")
    
    migration_content = migration_stub.format(table=table_name, schema=table_def['schema'], down_schema=down_schema)
    
    with open(migration_file, "w") as f:
        f.write(migration_content)
        
print("Generated all models and migrations sequentially.")
