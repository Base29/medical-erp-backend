<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocumNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'locum',
        'note',
    ];

    public function locum()
    {
        return $this->belongsTo(User::class, 'locum', 'id');
    }
}