<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlacklistedLocum extends Model
{
    use HasFactory;

    protected $fillable = [
        'locum',
    ];
}