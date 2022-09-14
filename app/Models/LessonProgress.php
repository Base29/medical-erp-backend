<?php

namespace App\Models;

use App\Models\ModuleLesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LessonProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lesson',
        'user',
        'completed_at',
        'completion_evidence',
    ];

    public function lessons()
    {
        return $this->belongsTo(ModuleLesson::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function alreadyRecordedProgress($lessonId, $userId)
    {
        $progressRecorded = $this->where(['lesson' => $lessonId, 'user' => $userId])->first();

        if ($progressRecorded === null) {
            return false;
        } else {
            return true;
        }
    }
}