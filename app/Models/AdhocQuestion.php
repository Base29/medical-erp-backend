<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdhocQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'schedule',
        'question',
        'answer',
    ];

    public function interviewSchedule()
    {
        return $this->belongsTo(interviewSchedule::class);
    }
}