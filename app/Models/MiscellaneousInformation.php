<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MiscellaneousInformation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_description',
        'interview_notes',
        'offer_letter_email',
        'job_advertisement',
        'health_questionnaire',
        'annual_declaration',
        'employee_confidentiality_agreement',
        'employee_privacy_notice',
        'locker_key_agreement',
        'is_locker_key_assigned',
        'equipment_provided_policy',
        'resume',
        'proof_of_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}