<?php

namespace App\Models;

use App\Models\Department;
use App\Models\Interview;
use App\Models\InterviewSchedule;
use App\Models\JobSpecification;
use App\Models\Offer;
use App\Models\PersonSpecification;
use App\Models\Practice;
use App\Models\Profile;
use App\Models\WorkPattern;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HiringRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'practice_id',
        'job_title',
        'contract_type',
        'department_id',
        'reporting_to',
        'start_date',
        'starting_salary',
        'reason_for_recruitment',
        'comment',
        'job_specification_id',
        'person_specification_id',
        'rota_information',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function workPatterns()
    {
        return $this->belongsToMany(WorkPattern::class);
    }

    public function jobSpecification()
    {
        return $this->belongsTo(JobSpecification::class);
    }

    public function personSpecification()
    {
        return $this->belongsTo(PersonSpecification::class);
    }

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    public function interviewSchedules()
    {
        return $this->hasMany(InterviewSchedule::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}