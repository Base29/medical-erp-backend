<?php

namespace App\Models;

use App\Models\PersonSpecification;
use App\Models\Practice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonSpecification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function personSpecificationAttributes()
    {
        return $this->hasMany(PersonSpecification::class);
    }

    public function practices()
    {
        return $this->belongsToMany(Practice::class);
    }
}