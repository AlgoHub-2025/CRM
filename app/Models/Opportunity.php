<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opportunity extends Model
{
    use HasFactory, HasUlids, SoftDeletes;
    protected $guarded = [];
    protected function casts(): array { return ['expected_close_date' => 'date']; }

    public function lead() { return $this->belongsTo(Lead::class); }
    public function client() { return $this->belongsTo(Client::class); }
    public function assignedTo() { return $this->belongsTo(Employee::class, 'assigned_to'); }
    public function stage() { return $this->belongsTo(PipelineStage::class, 'stage_id'); }
    public function proposals() { return $this->hasMany(Proposal::class); }
    
    public function activities() { return $this->morphMany(Activity::class, 'subject'); }
    public function followUps() { return $this->morphMany(FollowUp::class, 'subject'); }

    /**
     * Get the forecast value of the opportunity.
     */
    protected function forecastValue(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => $this->value * ($this->probability / 100),
        );
    }

    /**
     * Scope a query to only include opportunities visible to the given user.
     */
    public function scopeVisibleTo($query, User $user, string $action = 'view')
    {
        if ($user->hasPermissionTo("opportunities.{$action}.all")) {
            return $query;
        }

        if ($user->hasPermissionTo("opportunities.{$action}.own") && $user->employee) {
            return $query->where('assigned_to', $user->employee->id);
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * Check if this specific opportunity is visible to the given user.
     */
    public function isVisibleTo(User $user, string $action = 'view'): bool
    {
        if ($user->hasPermissionTo("opportunities.{$action}.all")) {
            return true;
        }

        if ($user->hasPermissionTo("opportunities.{$action}.own") && $user->employee) {
            return $this->assigned_to === $user->employee->id;
        }

        return false;
    }
}