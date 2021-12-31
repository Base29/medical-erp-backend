<?php

namespace App\Models;

use App\Models\HiringRequest;
use App\Models\User;
use App\Models\WorkTiming;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkPattern extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'pivot',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function workTimings()
    {
        return $this->hasMany(WorkTiming::class);
    }

    public function hiringRequests()
    {
        return $this->belongsToMany(HiringRequest::class);
    }
}