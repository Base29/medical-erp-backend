<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'schedule',
        'question',
        'answer',
        'option',
    ];

    public function interviewSchedule()
    {
        return $this->belongsTo(InterviewSchedule::class);
    }

    public function interviewQuestion()
    {
        return $this->belongsTo(InterviewQuestion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }
}