<?php

namespace App\Models;

use App\Models\JobSpecification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobResponsibility extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_specification_id',
        'responsibility',
    ];

    public function jobSpecs()
    {
        return $this->belongsTo(JobSpecification::class);
    }
}