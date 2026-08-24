<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class ProjectMember extends Model
{
    use HasUlids;
    protected $guarded = [];

    public function project() { return $this->belongsTo(Project::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
}