<?php

namespace App\Models;

use App\Models\Practice;
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
        'department',
        'reporting_to',
        'start_date',
        'starting_salary',
        'reason_for_recruitment',
        'comment',
        'job_specification',
        'person_specification',
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
}