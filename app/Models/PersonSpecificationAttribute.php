<?php

namespace App\Models;

use App\Models\PersonSpecification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PersonSpecificationAttribute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'person_specification_id',
        'attribute',
        'essential',
        'desirable',
    ];

    public function personSpecification()
    {
        return $this->belongsTo(PersonSpecification::class);
    }
}