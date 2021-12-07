<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nationality extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'passport_number',
        'passport_country_of_issue',
        'passport_date_of_expiry',
        'is_uk_citizen',
        'right_to_work_status',
        'share_code',
        'date_issued',
        'date_checked',
        'expiry_date',
        'visa_required',
        'visa_number',
        'visa_start_date',
        'visa_expiry_date',
        'restrictions',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}