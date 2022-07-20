<?php

namespace App\Models;

use App\Models\AppraisalPolicy;
use App\Models\EmployeeHandbook;
use App\Models\InductionChecklist;
use App\Models\InterviewPolicy;
use App\Models\ItPolicy;
use App\Models\LocumSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

    protected $hidden = [
        'guard_name',
        'created_at',
        'updated_at',
        'pivot',
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

    public function handbooks()
    {
        return $this->belongsToMany(EmployeeHandbook::class);
    }

    public function itPolicies()
    {
        return $this->belongsToMany(ItPolicy::class);
    }

    public function inductionChecklists()
    {
        return $this->hasMany(InductionChecklist::class);
    }

    public function appraisalPolicy()
    {
        return $this->hasOne(AppraisalPolicy::class);
    }

    public function hasAppraisalPolicy()
    {
        return AppraisalPolicy::where('role', $this->id)->first();
    }
}