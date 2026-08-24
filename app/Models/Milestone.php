<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Milestone extends Model
{
    use HasUlids, SoftDeletes, HasFactory;
    protected $guarded = [];
    protected function casts(): array { return ['due_date' => 'date']; }

    public function project() { return $this->belongsTo(Project::class); }
    public function tasks() { return $this->hasMany(Task::class); }
}