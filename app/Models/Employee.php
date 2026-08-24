<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, HasUlids, SoftDeletes;
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
}