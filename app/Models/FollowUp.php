<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class FollowUp extends Model
{
    use HasUlids;
    protected $guarded = [];
    protected function casts(): array { return ['due_at' => 'datetime']; }

    public function activity() { return $this->belongsTo(Activity::class); }
    public function subject() { return $this->morphTo(); }
    public function employee() { return $this->belongsTo(Employee::class); }
}