<?php

namespace App\Models;

use App\Models\HiringRequest;
use App\Models\JobResponsibility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobSpecification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'salary_grade',
        'location',
        'total_hours',
        'job_purpose',
    ];

    public function hiringRequests()
    {
        return $this->hasMany(HiringRequest::class);
    }

    public function responsibilities()
    {
        return $this->hasMany(JobResponsibility::class);
    }
}