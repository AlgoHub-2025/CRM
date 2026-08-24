import os

models_dir = 'app/Models'

models = {
    'User': """<?php
namespace App\\Models;
use Database\\Factories\\UserFactory;
use Illuminate\\Database\\Eloquent\\Attributes\\Fillable;
use Illuminate\\Database\\Eloquent\\Attributes\\Hidden;
use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;
use Illuminate\\Foundation\\Auth\\User as Authenticatable;
use Illuminate\\Notifications\\Notifiable;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUlids;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    public function employee() { return $this->hasOne(Employee::class); }
}""",
    'Employee': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;
use Illuminate\\Database\\Eloquent\\SoftDeletes;

class Employee extends Model
{
    use HasUlids, SoftDeletes;
    protected $guarded = [];

    public function user() { return $this->belongsTo(User::class); }
    public function managedCompanies() { return $this->hasMany(Company::class, 'account_manager_id'); }
    public function assignedLeads() { return $this->hasMany(Lead::class, 'assigned_to'); }
    public function assignedOpportunities() { return $this->hasMany(Opportunity::class, 'assigned_to'); }
    public function activities() { return $this->hasMany(Activity::class); }
    public function followUps() { return $this->hasMany(FollowUp::class); }
    public function projectMemberships() { return $this->hasMany(ProjectMember::class); }
    public function managedProjects() { return $this->hasMany(Project::class, 'project_manager_id'); }
    public function assignedTasks() { return $this->hasMany(Task::class, 'assigned_to'); }
    public function taskComments() { return $this->hasMany(TaskComment::class); }
    public function receivedPayments() { return $this->hasMany(Payment::class, 'received_by'); }
    public function assignedTickets() { return $this->hasMany(SupportTicket::class, 'assigned_to'); }
    public function uploadedDocuments() { return $this->hasMany(Document::class, 'uploaded_by'); }
}""",
    'Company': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;
use Illuminate\\Database\\Eloquent\\SoftDeletes;

class Company extends Model
{
    use HasUlids, SoftDeletes;
    protected $guarded = [];

    public function accountManager() { return $this->belongsTo(Employee::class, 'account_manager_id'); }
    public function contacts() { return $this->hasMany(Contact::class); }
    public function leads() { return $this->hasMany(Lead::class); }
    public function clients() { return $this->hasMany(Client::class); }
}""",
    'Contact': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;
use Illuminate\\Database\\Eloquent\\SoftDeletes;

class Contact extends Model
{
    use HasUlids, SoftDeletes;
    protected $guarded = [];
    protected function casts(): array { return ['is_decision_maker' => 'boolean']; }

    public function company() { return $this->belongsTo(Company::class); }
}""",
    'LeadSource': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;

class LeadSource extends Model
{
    use HasUlids;
    protected $guarded = [];

    public function leads() { return $this->hasMany(Lead::class, 'source_id'); }
}""",
    'PipelineStage': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;

class PipelineStage extends Model
{
    use HasUlids;
    protected $guarded = [];

    public function leads() { return $this->hasMany(Lead::class, 'status_id'); }
    public function opportunities() { return $this->hasMany(Opportunity::class, 'stage_id'); }
}""",
    'Lead': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;
use Illuminate\\Database\\Eloquent\\SoftDeletes;

class Lead extends Model
{
    use HasUlids, SoftDeletes;
    protected $guarded = [];
    protected function casts(): array { return ['converted_at' => 'datetime']; }

    public function company() { return $this->belongsTo(Company::class); }
    public function source() { return $this->belongsTo(LeadSource::class, 'source_id'); }
    public function assignedTo() { return $this->belongsTo(Employee::class, 'assigned_to'); }
    public function status() { return $this->belongsTo(PipelineStage::class, 'status_id'); }
    
    public function activities() { return $this->morphMany(Activity::class, 'subject'); }
    public function followUps() { return $this->morphMany(FollowUp::class, 'subject'); }
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }
}""",
    'Opportunity': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;
use Illuminate\\Database\\Eloquent\\SoftDeletes;

class Opportunity extends Model
{
    use HasUlids, SoftDeletes;
    protected $guarded = [];
    protected function casts(): array { return ['expected_close_date' => 'date']; }

    public function lead() { return $this->belongsTo(Lead::class); }
    public function client() { return $this->belongsTo(Client::class); }
    public function assignedTo() { return $this->belongsTo(Employee::class, 'assigned_to'); }
    public function stage() { return $this->belongsTo(PipelineStage::class, 'stage_id'); }
    
    public function activities() { return $this->morphMany(Activity::class, 'subject'); }
    public function followUps() { return $this->morphMany(FollowUp::class, 'subject'); }
}""",
    'Activity': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;

class Activity extends Model
{
    use HasUlids;
    protected $guarded = [];
    protected function casts(): array { return ['occurred_at' => 'datetime']; }

    public function subject() { return $this->morphTo(); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function followUps() { return $this->hasMany(FollowUp::class); }
}""",
    'FollowUp': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;

class FollowUp extends Model
{
    use HasUlids;
    protected $guarded = [];
    protected function casts(): array { return ['due_at' => 'datetime']; }

    public function activity() { return $this->belongsTo(Activity::class); }
    public function subject() { return $this->morphTo(); }
    public function employee() { return $this->belongsTo(Employee::class); }
}""",
    'Client': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;
use Illuminate\\Database\\Eloquent\\SoftDeletes;

class Client extends Model
{
    use HasUlids, SoftDeletes;
    protected $guarded = [];

    public function company() { return $this->belongsTo(Company::class); }
    public function primaryContact() { return $this->belongsTo(Contact::class, 'primary_contact_id'); }
    public function convertedFromOpportunity() { return $this->belongsTo(Opportunity::class, 'converted_from_opportunity_id'); }
    
    public function proposals() { return $this->hasMany(Proposal::class); }
    public function contracts() { return $this->hasMany(Contract::class); }
    public function projects() { return $this->hasMany(Project::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function supportTickets() { return $this->hasMany(SupportTicket::class); }
    
    public function activities() { return $this->morphMany(Activity::class, 'subject'); }
    public function followUps() { return $this->morphMany(FollowUp::class, 'subject'); }
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }
}""",
    'Proposal': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;
use Illuminate\\Database\\Eloquent\\SoftDeletes;

class Proposal extends Model
{
    use HasUlids, SoftDeletes;
    protected $guarded = [];
    protected function casts(): array { return ['valid_until' => 'date']; }

    public function client() { return $this->belongsTo(Client::class); }
    public function items() { return $this->hasMany(ProposalItem::class); }
    public function contracts() { return $this->hasMany(Contract::class); }
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }
}""",
    'ProposalItem': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;

class ProposalItem extends Model
{
    use HasUlids;
    protected $guarded = [];

    public function proposal() { return $this->belongsTo(Proposal::class); }
}""",
    'Contract': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;
use Illuminate\\Database\\Eloquent\\SoftDeletes;

class Contract extends Model
{
    use HasUlids, SoftDeletes;
    protected $guarded = [];
    protected function casts(): array { return ['start_date' => 'date', 'end_date' => 'date']; }

    public function client() { return $this->belongsTo(Client::class); }
    public function proposal() { return $this->belongsTo(Proposal::class); }
    public function projects() { return $this->hasMany(Project::class); }
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }
}""",
    'Project': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;
use Illuminate\\Database\\Eloquent\\SoftDeletes;

class Project extends Model
{
    use HasUlids, SoftDeletes;
    protected $guarded = [];
    protected function casts(): array { return ['start_date' => 'date', 'deadline' => 'date']; }

    public function client() { return $this->belongsTo(Client::class); }
    public function contract() { return $this->belongsTo(Contract::class); }
    public function projectManager() { return $this->belongsTo(Employee::class, 'project_manager_id'); }
    
    public function members() { return $this->hasMany(ProjectMember::class); }
    public function milestones() { return $this->hasMany(Milestone::class); }
    public function tasks() { return $this->hasMany(Task::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function supportTickets() { return $this->hasMany(SupportTicket::class); }
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }
}""",
    'ProjectMember': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;

class ProjectMember extends Model
{
    use HasUlids;
    protected $guarded = [];

    public function project() { return $this->belongsTo(Project::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
}""",
    'Milestone': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;

class Milestone extends Model
{
    use HasUlids;
    protected $guarded = [];
    protected function casts(): array { return ['due_date' => 'date']; }

    public function project() { return $this->belongsTo(Project::class); }
    public function tasks() { return $this->hasMany(Task::class); }
}""",
    'Task': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;

class Task extends Model
{
    use HasUlids;
    protected $guarded = [];
    protected function casts(): array { return ['deadline' => 'date']; }

    public function project() { return $this->belongsTo(Project::class); }
    public function milestone() { return $this->belongsTo(Milestone::class); }
    public function assignedTo() { return $this->belongsTo(Employee::class, 'assigned_to'); }
    public function comments() { return $this->hasMany(TaskComment::class); }
}""",
    'TaskComment': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;

class TaskComment extends Model
{
    use HasUlids;
    protected $guarded = [];

    public function task() { return $this->belongsTo(Task::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
}""",
    'Invoice': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;
use Illuminate\\Database\\Eloquent\\SoftDeletes;

class Invoice extends Model
{
    use HasUlids, SoftDeletes;
    protected $guarded = [];
    protected function casts(): array { return ['issue_date' => 'date', 'due_date' => 'date']; }

    public function client() { return $this->belongsTo(Client::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function items() { return $this->hasMany(InvoiceItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }
}""",
    'InvoiceItem': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;

class InvoiceItem extends Model
{
    use HasUlids;
    protected $guarded = [];

    public function invoice() { return $this->belongsTo(Invoice::class); }
}""",
    'Payment': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;

class Payment extends Model
{
    use HasUlids;
    protected $guarded = [];
    protected function casts(): array { return ['paid_at' => 'datetime']; }

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function client() { return $this->belongsTo(Client::class); }
    public function receivedBy() { return $this->belongsTo(Employee::class, 'received_by'); }
}""",
    'SupportTicket': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;
use Illuminate\\Database\\Eloquent\\SoftDeletes;

class SupportTicket extends Model
{
    use HasUlids, SoftDeletes;
    protected $guarded = [];
    protected function casts(): array { return ['resolved_at' => 'datetime']; }

    public function client() { return $this->belongsTo(Client::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function assignedTo() { return $this->belongsTo(Employee::class, 'assigned_to'); }
    public function messages() { return $this->hasMany(TicketMessage::class, 'ticket_id'); }
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }
}""",
    'TicketMessage': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;

class TicketMessage extends Model
{
    use HasUlids;
    protected $guarded = [];

    public function ticket() { return $this->belongsTo(SupportTicket::class); }
    public function sender() { return $this->morphTo(); }
}""",
    'Document': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;

class Document extends Model
{
    use HasUlids;
    protected $guarded = [];

    public function documentable() { return $this->morphTo(); }
    public function uploadedBy() { return $this->belongsTo(Employee::class, 'uploaded_by'); }
}""",
    'AuditLog': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;

class AuditLog extends Model
{
    use HasUlids;
    protected $guarded = [];
    protected function casts(): array { return ['old_values' => 'json', 'new_values' => 'json']; }

    public function user() { return $this->belongsTo(User::class); }
}""",
    'Setting': """<?php
namespace App\\Models;
use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Concerns\\HasUlids;

class Setting extends Model
{
    use HasUlids;
    protected $guarded = [];
}"""
}

for model, content in models.items():
    with open(f"{models_dir}/{model}.php", "w") as f:
        f.write(content)

print("Generated relationships for all models.")
