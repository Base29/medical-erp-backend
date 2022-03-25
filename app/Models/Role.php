<?php

namespace App\Models;

use App\Models\InductionChecklist;
use App\Models\InterviewPolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;

    protected $hidden = [
        'guard_name',
        'created_at',
        'updated_at',
    ];

    public function inductionChecklist()
    {
        return $this->hasMany(InductionChecklist::class);
    }

    public function interviewPolicy()
    {
        return $this->hasOne(InterviewPolicy::class);
    }

    public function hasInterviewPolicy()
    {
        return InterviewPolicy::where('role_id', $this->id)->first();
    }

    public function locumSessions()
    {
        return $this->hasMany(LocumSession::class);
    }
}