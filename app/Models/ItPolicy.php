<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItPolicy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'detail',
        'url',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function signatures()
    {
        return $this->belongsToMany(User::class);
    }
}