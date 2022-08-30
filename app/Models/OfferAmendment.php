<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfferAmendment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'offer',
        'status',
        'amount',
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer', 'id');
    }
}