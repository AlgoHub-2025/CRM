<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class ProposalItem extends Model
{
    use HasFactory, HasUlids;
    protected $guarded = [];

    public function proposal() { return $this->belongsTo(Proposal::class); }
}