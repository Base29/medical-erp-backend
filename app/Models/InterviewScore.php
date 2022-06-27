<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewScore extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cultural_fit',
        'career_motivation',
        'social_skills',
        'team_work',
        'technical_skills',
        'leadership_capability',
        'critical_thinking_problem_solving',
        'self_awareness',
        'total',
    ];

    public function interview()
    {
        return $this->belongsTo(InterviewSchedule::class);
    }
}