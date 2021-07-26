<?php

namespace App\Models;

use App\Models\Practice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Policy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'attachment',
    ];

    public function practice()
    {
        return $this->belongsToMany(Practice::class);
    }
}