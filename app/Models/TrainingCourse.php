<?php

namespace App\Models;

use App\Models\CourseModule;
use App\Models\CourseProgress;
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
        'frequency',
        'type',
    ];

    public function modules()
    {
        return $this->hasMany(CourseModule::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function progress()
    {
        return $this->hasMany(CourseProgress::class);
    }
}