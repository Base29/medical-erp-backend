<?php

namespace App\Models;

use App\Models\Appraisal;
use App\Models\AppraisalQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppraisalAnswer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'appraisal',
        'question',
        'answer',
        'option',
    ];

    public function appraisal()
    {
        return $this->belongsTo(Appraisal::class);
    }

    public function appraisalQuestion()
    {
        return $this->belongsTo(AppraisalQuestion::class);
    }
}