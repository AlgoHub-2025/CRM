<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Activity extends Model
{
    use HasUlids;
    protected $guarded = [];
    protected function casts(): array { return ['occurred_at' => 'datetime']; }

    public function subject() { return $this->morphTo(); }
    public function employee() { return $this->belongsTo(Employee::class); }
    public function followUps() { return $this->hasMany(FollowUp::class); }
}