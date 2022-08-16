<?php

namespace App\Models;

use App\Models\ModuleLesson;
use App\Models\ModuleProgress;
use App\Models\TrainingCourse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseModule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course',
        'name',
        'duration',
        'is_required',
        'frequency',
        'reminder',
    ];

    public function lessons()
    {
        return $this->hasMany(ModuleLesson::class, 'module', 'id');
    }

    public function course()
    {
        return $this->belongsTo(TrainingCourse::class, 'course', 'id');
    }

    public function progress()
    {
        return $this->hasMany(ModuleProgress::class);
    }
}