<?php

namespace App\Models;

use App\Models\CourseModule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleProgress extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'module',
        'user',
        'completed_at',
        'completion_evidence',
    ];

    public function modules()
    {
        return $this->belongsTo(CourseModule::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function alreadyRecordedProgress($moduleId, $userId)
    {
        $progressRecorded = $this->where(['module' => $moduleId, 'user' => $userId])->first();

        if ($progressRecorded === null) {
            return false;
        } else {
            return true;
        }
    }
}