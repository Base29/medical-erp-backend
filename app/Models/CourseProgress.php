<?php

namespace App\Models;

use App\Models\TrainingCourse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course',
        'user',
        'completed_at',
        'completion_evidence',
    ];

    public function course()
    {
        return $this->belongsTo(TrainingCourse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}