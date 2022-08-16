<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleLesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'module',
        'name',
        'start_date',
        'due_date',
        'description',
        'url',
    ];

    public function module()
    {
        return $this->belongsTo(
            CourseModule::class, 'module', 'id'
        );
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }
}