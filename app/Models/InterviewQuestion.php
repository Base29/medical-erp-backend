<?php

namespace App\Models;

use App\Models\InterviewPolicy;
use App\Models\InterviewQuestionOption;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'interview_policy_id',
        'type',
        'question',
    ];

    public function interviewPolicy()
    {
        return $this->belongsTo(InterviewPolicy::class);
    }

    public function options()
    {
        return $this->hasMany(InterviewQuestionOption::class);
    }

    public function interviewAnswers()
    {
        return $this->hasMany(InterviewAnswer::class, 'question', 'id');
    }
}