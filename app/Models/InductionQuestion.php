<?php

namespace App\Models;

use App\Models\InductionChecklist;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InductionQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'induction_checklist_id',
        'question',
    ];

    public function inductionChecklist()
    {
        return $this->belongsTo(InductionChecklist::class);
    }
}