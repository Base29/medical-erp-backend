<?php

namespace App\Models;

use App\Models\Equipment;
use App\Models\Policy;
use App\Models\Post;
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

    protected $hidden = [
        'pivot',
        'deleted_at',
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

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }

}