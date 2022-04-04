<?php

namespace App\Models;

use App\Models\Department;
use App\Models\HiringRequest;
use App\Models\InterviewPolicy;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'interview_policy_id',
        'practice_id',
        'hiring_request_id',
        'user_id',
        'date',
        'time',
        'location',
        'interview_type',
        'application_status',
        'is_completed',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    // public function interview()
    // {
    //     return $this->belongsTo(Interview::class);
    // }

    public function hiringRequest()
    {
        return $this->belongsTo(HiringRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function interviewPolicy()
    {
        return $this->belongsTo(InterviewPolicy::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}