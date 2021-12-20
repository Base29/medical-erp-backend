<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Education extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'institution',
        'subject',
        'start_date',
        'completion_date',
        'degree',
        'grade',
        'certificate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}