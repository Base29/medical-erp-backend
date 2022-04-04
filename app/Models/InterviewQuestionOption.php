<?php

namespace App\Models;

use App\Models\InterviewQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewQuestionOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'interview_question_id',
        'option',
    ];

    public function interviewQuestion()
    {
        return $this->belongsTo(InterviewQuestion::class);
    }
}