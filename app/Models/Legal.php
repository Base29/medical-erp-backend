<?php

namespace App\Models;

use App\Models\NmcQualification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Legal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'is_nurse',
        'name',
        'location',
        'expiry_date',
        'registration_status',
        'register_entry',
        'nmc_document',
        'gmc_reference_number',
        'gp_register_date',
        'specialist_register',
        'provisional_registration_date',
        'full_registration_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nmcQualifications()
    {
        return $this->hasMany(NmcQualification::class);
    }
}