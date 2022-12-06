<?php

namespace App\Models;

use App\Models\AppraisalPolicy;
use App\Models\Department;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appraisal extends Model
{
    use HasFactory, SoftDeletes;

    public function practice()
    {
        return $this->belongsTo(Practice::class, 'practice', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }

    public function appraisalPolicies()
    {
        return $this->belongsToMany(AppraisalPolicy::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department', 'id');
    }

    public function appraisalAnswers()
    {
        return $this->hasMany(AppraisalAnswer::class);
    }

    public function userObjectives()
    {
        return $this->hasManyThrough(UserObjective::class, User::class);
    }
}