<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Document extends Model
{
    use HasUlids;
    protected $guarded = [];

    public function documentable() { return $this->morphTo(); }
    public function uploadedBy() { return $this->belongsTo(Employee::class, 'uploaded_by'); }
}