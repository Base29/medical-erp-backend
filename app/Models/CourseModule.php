<?php

namespace App\Models;

use App\Models\CourseModuleExam;
use App\Models\ModuleLesson;
use App\Models\ModuleProgress;
use App\Models\TrainingCourse;
use App\Models\User;
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
        'is_exam_required',
    ];

    public function lessons()
    {
        return $this->hasMany(ModuleLesson::class, 'module', 'id');
    }

    public function course()
    {
        return $this->belongsTo(TrainingCourse::class, 'course', 'id');
    }

    public function moduleProgress()
    {
        return $this->hasMany(ModuleProgress::class, 'module', 'id');
    }

    public function endOfModuleExams()
    {
        return $this->hasMany(CourseModuleExam::class);
    }

    public function userModuleProgress()
    {
        return $this->hasManyThrough(User::class, ModuleProgress::class, 'user', 'id', 'module', 'id');
    }
}