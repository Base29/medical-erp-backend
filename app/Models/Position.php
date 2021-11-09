<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Position extends Model
{
    use HasFactory SoftDeletes;

    protected $fillable = [
        'title',
        'title',
        'type',
        'department',
        'reports_to',
        'probation_end_date',
        'notice_period'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}