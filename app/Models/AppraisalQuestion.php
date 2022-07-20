<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppraisalQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'policy',
        'type',
        'head',
        'question',
    ];

    public function policy()
    {
        return $this->belongsTo(AppraisalPolicy::class);
    }

    public function options()
    {
        return $this->hasMany(AppraisalQuestionOption::class, 'question', 'id');
    }

    public function appraisalAnswers()
    {
        return $this->hasMany(AppraisalAnswer::class, 'question', 'id');
    }
}