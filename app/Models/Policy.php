<?php

namespace App\Models;

use App\Models\Practice;
use App\Models\Signature;
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

    protected $hidden = [
        'pivot',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function signatures()
    {
        return $this->hasMany(Signature::class);
    }
}