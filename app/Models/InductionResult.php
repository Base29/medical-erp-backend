<?php

namespace App\Models;

use App\Models\InductionChecklist;
use App\Models\InductionQuestion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InductionResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'induction_checklist_id',
        'induction_question_id',
        'completed',
        'comment',
    ];

    public function inductionQuestion()
    {
        return $this->belongsTo(InductionQuestion::class);
    }

    public function inductionChecklist()
    {
        return $this->belongsTo(InductionChecklist::class);
    }
}