<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model
{
    use HasUlids, SoftDeletes, HasFactory;
    protected $guarded = [];
    protected function casts(): array { return ['deadline' => 'date']; }

    public function project() { return $this->belongsTo(Project::class); }
    public function milestone() { return $this->belongsTo(Milestone::class); }
    public function assignedTo() { return $this->belongsTo(Employee::class, 'assigned_to'); }
    public function comments() { return $this->hasMany(TaskComment::class); }
}