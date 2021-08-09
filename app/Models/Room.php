<?php

namespace App\Models;

use App\Models\Practice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'practice_id',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function checkLists()
    {
        return $this->hasMany(CheckList::class);
    }
}