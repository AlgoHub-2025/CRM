<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PipelineStage extends Model
{
    use HasFactory, HasUlids;
    protected $guarded = [];
    
    protected $casts = [
        'order' => 'integer',
        'is_won' => 'boolean',
    ];

    public function leads() { return $this->hasMany(Lead::class, 'status_id'); }
    public function opportunities() { return $this->hasMany(Opportunity::class, 'stage_id'); }
}