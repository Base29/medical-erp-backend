<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'interview_schedule_id',
        'interview_question_id',
        'answer',
        'interview_question_option_id',
    ];

    public function interviewSchedule()
    {
        return $this->belongsTo(InterviewSchedule::class);
    }

    public function interviewQuestion()
    {
        return $this->belongsTo(InterviewQuestion::class);
    }
}