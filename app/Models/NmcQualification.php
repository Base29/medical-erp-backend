<?php

namespace App\Models;

use App\Models\Legal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NmcQualification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'legal_id',
        'qualification',
        'qualification_date',
    ];

    public function legal()
    {
        return $this->belongsTo(Legal::class);
    }
}