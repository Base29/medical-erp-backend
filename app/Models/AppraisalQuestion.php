<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppraisalQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'appraisal_policy',
        'type',
        'head',
        'question',
    ];

    public function appraisalPolicy()
    {
        return $this->belongsTo(AppraisalPolicy::class);
    }

    public function options()
    {
        return $this->hasMany(AppraisalQuestionOption::class);
    }

    public function appraisalAnswers()
    {
        return $this->hasMany(AppraisalAnswer::class, 'question', 'id');
    }
}