<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class LeadSource extends Model
{
    use HasUlids;
    protected $guarded = [];

    public function leads() { return $this->hasMany(Lead::class, 'source_id'); }
}