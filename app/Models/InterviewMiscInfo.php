<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewMiscInfo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'current_salary',
        'expected_salary',
        'difference',
        'availability',
        'available_time',
        'job_type',
        'dbs',
        'dismissals',
        'given_notice',
        'notice_start',
        'notice_duration',
        'interviewing_elsewhere',
        'salary_notes',
        'notice_notes',
    ];

    protected $casts = [
        'availability' => 'array',
    ];

    public function interview()
    {
        return $this->belongsTo(InterviewSchedule::class);
    }
}