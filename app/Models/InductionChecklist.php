<?php

namespace App\Models;

use App\Models\InductionQuestion;
use App\Models\InductionSchedule;
use App\Models\Practice;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InductionChecklist extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'practice_id',
        'role_id',
        'name',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function inductionQuestions()
    {
        return $this->hasMany(InductionQuestion::class);
    }

    public function inductionSchedules()
    {
        return $this->belongsToMany(InductionSchedule::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}