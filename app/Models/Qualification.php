<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user',
        'qualification',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}