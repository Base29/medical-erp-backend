<?php

namespace App\Models;

use App\Models\CourseModule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseModuleExam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'module',
        'user',
        'type',
        'number_of_questions',
        'is_restricted',
        'duration',
        'description',
        'url',
        'is_passing_percentage',
        'passing_percentage',
        'is_passed',
        'grade_achieved',
        'percentage_achieved',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user', 'id');
    }

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'module', 'id');
    }
}