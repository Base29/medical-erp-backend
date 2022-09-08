<?php

namespace App\Models;

use App\Models\Answer;
use App\Models\Comment;
use App\Models\ContractSummary;
use App\Models\Department;
use App\Models\Education;
use App\Models\EmergencyContact;
use App\Models\EmployeeHandbook;
use App\Models\EmploymentCheck;
use App\Models\EmploymentHistory;
use App\Models\EmploymentPolicy;
use App\Models\Equipment;
use App\Models\HiringRequest;
use App\Models\InductionSchedule;
use App\Models\InterviewSchedule;
use App\Models\ItPolicy;
use App\Models\Legal;
use App\Models\LocumSession;
use App\Models\MiscellaneousInformation;
use App\Models\Offer;
use App\Models\PositionSummary;
use App\Models\Post;
use App\Models\Practice;
use App\Models\Profile;
use App\Models\Reference;
use App\Models\Signature;
use App\Models\Termination;
use App\Models\WorkPattern;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pivot',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $guard_name = 'api';

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function sendPasswordResetNotification($token)
    {
        $url = env('FRONTEND_URL') . '/reset-password?token=' . $token . '&email=' . $this["email"];
        $this->notify(new ResetPasswordNotification($url));
    }

    public function practices()
    {
        return $this->belongsToMany(Practice::class);
    }

    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function isSuperAdmin()
    {
        return $this->roles->contains('name', 'super_admin');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'id');
    }

    public function positionSummary()
    {
        return $this->hasOne(PositionSummary::class, 'user_id', 'id');
    }

    public function contractSummary()
    {
        return $this->hasOne(ContractSummary::class, 'user_id', 'id');
    }

    public function workPatterns()
    {
        return $this->belongsToMany(WorkPattern::class);
    }

    public function miscInfo()
    {
        return $this->hasOne(MiscellaneousInformation::class);
    }

    public function employmentCheck()
    {
        return $this->hasOne(EmploymentCheck::class);
    }

    public function employmentPolicies()
    {
        return $this->hasMany(EmploymentPolicy::class);
    }

    public function employmentHistories()
    {
        return $this->hasMany(EmploymentHistory::class);
    }

    public function equipment()
    {
        return $this->belongsToMany(Equipment::class);
    }

    public function references()
    {
        return $this->hasMany(Reference::class);
    }

    public function education()
    {
        return $this->hasMany(Education::class);
    }

    public function legal()
    {
        return $this->hasOne(Legal::class);
    }

    public function emergencyContacts()
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function termination()
    {
        return $this->hasOne(Termination::class);
    }

    public function inductionSchedule()
    {
        return $this->hasOne(InductionSchedule::class);
    }

    public function inductionAlreadyScheduled()
    {

        $schedules = InductionSchedule::get();

        $alreadyHasInductionSchedule = $schedules->contains('user_id', $this->id);

        return $alreadyHasInductionSchedule;
    }

    public function interviewSchedule()
    {
        return $this->hasOne(InterviewSchedule::class);
    }

    public function offer()
    {
        return $this->hasOne(Offer::class);
    }

    public function isPracticeManager()
    {
        $practiceManager = $this->with('practices')
            ->whereHas('practices', function ($q) {
                $q->where('type', 'practice-manager');
            })
            ->first();

        if ($practiceManager === null) {
            return false;
        } else {
            return true;
        }

    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function hiringRequests()
    {
        return $this->hasMany(HiringRequest::class);
    }

    public function locumSessions()
    {
        return $this->belongsToMany(LocumSession::class);
    }

    public function signedEmployeeHandbooks()
    {
        return $this->belongsToMany(EmployeeHandbook::class);
    }

    public function signedItPolicies()
    {
        return $this->belongsToMany(ItPolicy::class);
    }

    public function courses()
    {
        return $this->belongsToMany(TrainingCourse::class);
    }

    public function courseProgress()
    {
        return $this->hasMany(CourseProgress::class);
    }

    public function moduleProgress()
    {
        return $this->hasMany(ModuleProgress::class);
    }

    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function endOfModuleExams()
    {
        return $this->hasMany(CourseModuleExam::class);
    }

    public function testingLogicProgress()
    {
        return $this->hasManyThrough(LessonProgress::class, ModuleLesson::class);
    }

    public function interviewAnswers()
    {
        return $this->hasMany(InterviewAnswer::class);
    }

    public function sessionInvites()
    {
        return $this->hasMany(LocumSessionInvite::class);
    }

    public function createdSessionInvites()
    {
        return $this->hasMany(LocumSessionInvite::class);
    }
}