<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SupportTicket extends Model
{
    use HasUlids, SoftDeletes, HasFactory;
    protected $guarded = [];
    protected function casts(): array { return ['resolved_at' => 'datetime']; }

    public function client() { return $this->belongsTo(Client::class); }
    public function project() { return $this->belongsTo(Project::class); }
    public function assignedTo() { return $this->belongsTo(Employee::class, 'assigned_to'); }
    public function messages() { return $this->hasMany(TicketMessage::class, 'ticket_id'); }
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }
}