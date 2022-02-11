<?php

namespace App\Models;

use App\Models\InterviewQuestion;
use App\Models\InterviewSchedule;
use App\Models\Practice;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InterviewPolicy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'practice_id',
        'role_id',
        'name',
    ];

    public function interviewSchedules()
    {
        return $this->hasMany(InterviewSchedule::class);
    }

    public function interviewQuestions()
    {
        return $this->hasMany(InterviewQuestion::class);
    }

    public function interviewPolicy()
    {
        return $this->belongsTo(Practice::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }
}