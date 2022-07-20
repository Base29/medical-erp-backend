<?php

namespace App\Models;

use App\Models\Appraisal;
use App\Models\AppraisalQuestion;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppraisalPolicy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'role',
        'name',
    ];

    protected $hidden = [
        'pivot',
    ];

    public function appraisal()
    {
        return $this->belongsToMany(Appraisal::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role', 'id');
    }

    public function questions()
    {
        return $this->hasMany(AppraisalQuestion::class, 'policy', 'id');
    }
}