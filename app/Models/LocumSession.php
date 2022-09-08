<?php

namespace App\Models;

use App\Models\Practice;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocumSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'practice',
        'role',
        'quantity',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'rate',
        'unit',
        'location',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function locums()
    {
        return $this->belongsToMany(User::class);
    }

    public function userAlreadyAssignedToSession($id)
    {
        return $this->locums->contains('id', $id);
    }

    public function invitedLocums()
    {
        return $this->hasMany(LocumSessionInvite::class);
    }
}