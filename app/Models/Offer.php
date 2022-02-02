<?php

namespace App\Models;

use App\Models\HiringRequest;
use App\Models\Practice;
use App\Models\User;
use App\Models\WorkPattern;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hiring_request_id',
        'user_id',
        'work_pattern_id',
        'status',
    ];

    public function hiringRequest()
    {
        return $this->belongsTo(HiringRequest::class);
    }

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workPattern()
    {
        return $this->belongsTo(WorkPattern::class);
    }
}