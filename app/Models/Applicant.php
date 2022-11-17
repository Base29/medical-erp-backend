<?php

namespace App\Models;

use App\Models\HiringRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Applicant extends User
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

}