<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmploymentHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employer_name',
        'address',
        'phone_number',
        'type_of_business',
        'job_title',
        'job_start_date',
        'job_end_date',
        'salary',
        'reporting_to',
        'notice_period',
        'can_contact_referee',
        'reason_for_leaving',
        'is_current',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}