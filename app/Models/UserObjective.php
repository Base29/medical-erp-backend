<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserObjective extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'appraisal',
        'user',
        'objective',
        'due_date',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appraisal()
    {
        return $this->belongsTo(Appraisal::class);
    }
}