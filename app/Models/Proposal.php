<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposal extends Model
{
    use HasFactory, HasUlids, SoftDeletes;
    protected $guarded = [];
    protected function casts(): array { return ['valid_until' => 'date']; }

    public function client() { return $this->belongsTo(Client::class); }
    public function opportunity() { return $this->belongsTo(Opportunity::class); }
    public function items() { return $this->hasMany(ProposalItem::class); }
    public function contracts() { return $this->hasMany(Contract::class); }
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }
}