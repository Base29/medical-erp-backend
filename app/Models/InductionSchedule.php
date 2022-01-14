<?php

namespace App\Models;

use App\Models\InductionChecklist;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InductionSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'induction_checklist_id',
        'user_id',
        'date',
        'time',
        'duration',
        'is_hq_required',
        'hq_staff_role_id',
        'hq_staff_id',
        'is_additional_staff_required',
        'additional_staff_role_id',
        'additional_staff_id',
        'is_completed',
        'completed_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inductionChecklist()
    {
        return $this->belongsTo(InductionChecklist::class);
    }
}