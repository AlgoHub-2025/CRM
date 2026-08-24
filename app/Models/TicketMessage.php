<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketMessage extends Model
{
    use HasUlids, HasFactory;
    protected $guarded = [];

    public function ticket() { return $this->belongsTo(SupportTicket::class); }
    public function sender() { return $this->morphTo(); }
}