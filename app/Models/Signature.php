<?php

namespace App\Models;

use App\Models\Policy;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment',
    ];

    public function policies()
    {
        return $this->belongsToMany(Policy::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}