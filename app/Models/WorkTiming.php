<?php

namespace App\Models;

use App\Models\WorkPattern;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkTiming extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [];

    public function workPattern()
    {
        return $this->belongsTo(WorkPattern::class);
    }
}