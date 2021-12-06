<?php

namespace App\Models;

use App\Models\Answer;
use App\Models\Comment;
use App\Models\ContractSummary;
use App\Models\MiscellaneousInformation;
use App\Models\PositionSummary;
use App\Models\Post;
use App\Models\Practice;
use App\Models\Profile;
use App\Models\Signature;
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

    public function signedBy(User $user)
    {
        return $this->signatures->contains('user_id', $user->id);
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

}