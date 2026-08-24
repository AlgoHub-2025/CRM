<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, HasUlids, SoftDeletes;
    protected $guarded = [];

    public function company() { return $this->belongsTo(Company::class); }
    public function primaryContact() { return $this->belongsTo(Contact::class, 'primary_contact_id'); }
    public function convertedFromOpportunity() { return $this->belongsTo(Opportunity::class, 'converted_from_opportunity_id'); }
    
    public function opportunities() { return $this->hasMany(Opportunity::class); }
    public function proposals() { return $this->hasMany(Proposal::class); }
    public function contracts() { return $this->hasMany(Contract::class); }
    public function projects() { return $this->hasMany(Project::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function supportTickets() { return $this->hasMany(SupportTicket::class); }
    
    public function activities() { return $this->morphMany(Activity::class, 'subject'); }
    public function followUps() { return $this->morphMany(FollowUp::class, 'subject'); }
    public function documents() { return $this->morphMany(Document::class, 'documentable'); }

    public function totalContractValue()
    {
        return $this->contracts()->whereNotIn('status', ['draft', 'terminated'])->sum('value');
    }
}