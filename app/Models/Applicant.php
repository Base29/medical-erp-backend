<?php

namespace App\Models;

use App\Models\HiringRequest;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Applicant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hiring_request_id',
        'user_id',
    ];

    public function vacancy()
    {
        return $this->belongsTo(HiringRequest::class, 'hiring_request_id', 'id');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'user_id');
    }
}