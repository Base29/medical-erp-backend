<?php

namespace App\Models;

use App\Models\HiringRequest;
use App\Models\Practice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Interview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'practice_id',
        'hiring_request_id',
        'name',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function hiringRequest()
    {
        return $this->belongsTo(HiringRequest::class);
    }

    public function interviewSchedule()
    {
        return $this->hasOne(InterviewSchedule::class);
    }
}