<?php

namespace App\Models;

use App\Models\Policy;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Practice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'practice_name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function policies()
    {
        return $this->hasMany(Policy::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

}