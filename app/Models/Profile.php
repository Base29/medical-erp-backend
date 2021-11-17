<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'maiden_name',
        'last_name',
        'profile_image',
        'primary_role',
        'professional_email',
        'gender',
        'home_phone',
        'work_phone',
        'mobile_phone',
        'dob',
        'address',
        'city',
        'county',
        'country',
        'zip_code',
        'nhs_card',
        'nhs_number',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}