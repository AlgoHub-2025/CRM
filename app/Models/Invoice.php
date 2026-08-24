<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory, HasUlids, SoftDeletes;
    protected $guarded = [];
    protected function casts(): array { return ['issue_date' => 'date', 'due_date' => 'date', 'last_reminder_sent_at' => 'datetime']; }

    public function client() { return $this->belongsTo(Client::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function contract() { return $this->belongsTo(Contract::class); }
    public function items() { return $this->hasMany(InvoiceItem::class)->orderBy('sort_order'); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }
}