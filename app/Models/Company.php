<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory, HasUlids, SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'tax_number' => 'encrypted',
    ];

    public function accountManager()
    {
        return $this->belongsTo(Employee::class, 'account_manager_id');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    protected static function booted()
    {
        static::deleting(function ($company) {
            $company->contacts()->delete();
        });

        static::restoring(function ($company) {
            $company->contacts()->restore();
        });
    }
    public function leads() { return $this->hasMany(Lead::class); }
    public function clients() { return $this->hasMany(Client::class); }
}