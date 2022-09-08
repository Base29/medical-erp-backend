<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocumSessionInvite extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'session',
        'locum',
        'status',
        'title',
    ];

    public function session()
    {
        return $this->belongsTo(LocumSession::class, 'session', 'id');
    }

    public function locum()
    {
        return $this->belongsTo(User::class, 'locum', 'id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator', 'id');
    }
}