<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory, HasUlids;
    protected $guarded = [];
    protected function casts(): array { return [
        'paid_at' => 'datetime',
        'transaction_reference' => 'encrypted',
    ]; }

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function client() { return $this->belongsTo(Client::class); }
    public function receivedBy() { return $this->belongsTo(Employee::class, 'received_by'); }
}