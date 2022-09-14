<?php

namespace App\Models;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeHandbook extends Model
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