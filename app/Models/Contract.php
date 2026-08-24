<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, HasUlids, SoftDeletes;
    protected $guarded = [];
    protected function casts(): array { return ['start_date' => 'date', 'end_date' => 'date']; }

    public function client() { return $this->belongsTo(Client::class); }
    public function proposal() { return $this->belongsTo(Proposal::class); }
    public function projects() { return $this->hasMany(Project::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }
}