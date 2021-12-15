<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reference extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_type',
        'referee_name',
        'company_name',
        'relationship',
        'referee_job_title',
        'phone_number',
        'referee_email',
        'start_date',
        'end_date',
        'can_contact_referee',
        'reference_document',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}