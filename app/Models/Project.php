<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasUlids, SoftDeletes, HasFactory;
    protected $guarded = [];
    protected function casts(): array { return ['start_date' => 'date', 'deadline' => 'date']; }

    protected static function booted()
    {
        static::deleting(function ($project) {
            $project->milestones()->delete();
            $project->tasks()->delete();
        });
        
        static::restoring(function ($project) {
            $project->milestones()->restore();
            $project->tasks()->restore();
        });
    }

    public function client() { return $this->belongsTo(Client::class); }
    public function contract() { return $this->belongsTo(Contract::class); }
    public function projectManager() { return $this->belongsTo(Employee::class, 'project_manager_id'); }
    
    public function members() { return $this->hasMany(ProjectMember::class); }
    public function milestones() { return $this->hasMany(Milestone::class); }
    public function tasks() { return $this->hasMany(Task::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function supportTickets() { return $this->hasMany(SupportTicket::class); }
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }
}