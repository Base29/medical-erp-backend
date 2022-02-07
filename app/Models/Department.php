<?php

namespace App\Models;

use App\Models\HiringRequest;
use App\Models\Practice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'practice_id',
        'name',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function hiringRequests()
    {
        return $this->hasMany(HiringRequest::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}