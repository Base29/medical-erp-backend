<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppraisalReschedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reason',
        'date',
        'time',
        'duration',
        'location',
    ];

    public function appraisal()
    {
        return $this->belongsTo(Appraisal::class);
    }
}