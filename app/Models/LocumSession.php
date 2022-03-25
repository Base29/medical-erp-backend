<?php

namespace App\Models;

use App\Models\Practice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocumSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'practice',
        'role',
        'quantity',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'rate',
        'unit',
        'location',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}