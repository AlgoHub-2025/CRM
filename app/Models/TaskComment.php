<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class TaskComment extends Model
{
    use HasUlids;
    protected $guarded = [];

    public function task() { return $this->belongsTo(Task::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
}