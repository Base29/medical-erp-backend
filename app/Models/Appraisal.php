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
        return $this->belongsTo(Practice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appraisalPolicies()
    {
        return $this->belongsToMany(AppraisalPolicy::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function appraisalAnswers()
    {
        return $this->hasMany(AppraisalAnswer::class);
    }
}