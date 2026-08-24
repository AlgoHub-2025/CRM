<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory, HasUlids, SoftDeletes;
    
    protected $fillable = [
        'company_id',
        'name',
        'designation',
        'email',
        'phone',
        'whatsapp',
        'is_decision_maker',
        'notes',
    ];

    protected $casts = [
        'is_decision_maker' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}