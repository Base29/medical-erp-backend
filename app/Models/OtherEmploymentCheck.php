<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OtherEmploymentCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'driving_license_number',
        'driving_license_country_of_issue',
        'driving_license_class',
        'driving_license_date_of_expiry',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}