<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CandidateQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'schedule',
        'question',
    ];

    public function interviewSchedule()
    {
        return $this->belongsTo(interviewSchedule::class);
    }
}