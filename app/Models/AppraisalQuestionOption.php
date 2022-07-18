<?php

namespace App\Models;

use App\Models\AppraisalQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppraisalQuestionOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'appraisal_question',
        'option',
    ];

    public function appraisalQuestion()
    {
        return $this->belongsTo(AppraisalQuestion::class);
    }
}