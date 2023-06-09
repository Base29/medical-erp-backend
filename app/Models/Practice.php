<?php

namespace App\Models;

use App\Models\Department;
use App\Models\Equipment;
use App\Models\HiringRequest;
use App\Models\InductionChecklist;
use App\Models\InductionSchedule;
use App\Models\Interview;
use App\Models\InterviewPolicy;
use App\Models\InterviewSchedule;
use App\Models\Offer;
use App\Models\Policy;
use App\Models\Post;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Practice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'practice_name',
    ];

    protected $hidden = [
        'pivot',
        'deleted_at',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function policies()
    {
        return $this->hasMany(Policy::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }

    public function hiringRequests()
    {
        return $this->hasMany(HiringRequest::class);
    }

    public function inductionChecklists()
    {
        return $this->hasMany(InductionChecklist::class);
    }

    public function inductionSchedules()
    {
        return $this->hasMany(InductionSchedule::class);
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    public function interviewSchedules()
    {
        return $this->hasMany(InterviewSchedule::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function hasManager()
    {
        $practiceManager = $this->with('users')
            ->whereHas('users', function ($q) {
                $q->where('type', 'practice-manager');
            })
            ->first();

        if ($practiceManager === null) {
            return false;
        } else {
            return true;
        }
    }

    public function interviewPolicies()
    {
        return $this->hasMany(InterviewPolicy::class);
    }

    public function practiceManager()
    {
        return $this->belongsTo(User::class, 'practice_manager', 'id');
    }

    public function locumSessions()
    {
        return $this->hasMany(LocumSession::class);
    }

    public function appraisals()
    {
        return $this->hasMany(Appraisal::class, 'practice', 'id');
    }

    public function locumInvoices()
    {
        return $this->hasMany(LocumInvoice::class);
    }
}