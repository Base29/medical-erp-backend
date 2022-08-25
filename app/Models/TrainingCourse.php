<?php

namespace App\Models;

use App\Models\CourseModule;
use App\Models\CourseProgress;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingCourse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'role',
        'name',
        'description',
        'frequency',
        'type',
    ];

    protected $hidden = [
        'pivot',
    ];

    public function modules()
    {
        return $this->hasMany(CourseModule::class, 'course', 'id');
    }

    public function enrolledUsers()
    {
        return $this->belongsToMany(User::class);
    }

    public function courseProgress()
    {
        return $this->hasMany(CourseProgress::class, 'course', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function alreadyAssignedToCourse($user)
    {
        return $this->enrolledUsers->contains($user);
    }
}