<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    use HasFactory, HasUlids, SoftDeletes;
    protected $guarded = [];
    protected function casts(): array { return ['converted_at' => 'datetime']; }

    public function company() { return $this->belongsTo(Company::class); }
    public function source() { return $this->belongsTo(LeadSource::class, 'source_id'); }
    public function assignedTo() { return $this->belongsTo(Employee::class, 'assigned_to'); }
    public function status() { return $this->belongsTo(PipelineStage::class, 'status_id'); }
    
    public function activities() { return $this->morphMany(Activity::class, 'subject'); }
    public function followUps() { return $this->morphMany(FollowUp::class, 'subject'); }
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }

    /**
     * Scope a query to only include leads visible to the given user based on their permissions.
     */
    public function scopeVisibleTo($query, User $user, string $action = 'view')
    {
        if ($user->hasPermissionTo("leads.{$action}.all")) {
            return $query;
        }

        if ($user->hasPermissionTo("leads.{$action}.own") && $user->employee) {
            return $query->where('assigned_to', $user->employee->id);
        }

        // If they have neither permission, they see nothing.
        return $query->whereRaw('1 = 0');
    }

    /**
     * Check if this specific lead is visible to the given user based on their permissions.
     */
    public function isVisibleTo(User $user, string $action = 'view'): bool
    {
        if ($user->hasPermissionTo("leads.{$action}.all")) {
            return true;
        }

        if ($user->hasPermissionTo("leads.{$action}.own") && $user->employee) {
            return $this->assigned_to === $user->employee->id;
        }

        return false;
    }
}