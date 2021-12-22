<?php

namespace App\Models;

use App\Models\Legal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GmcSpecialistRegister extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'legal_id',
        'specialist_register',
        'specialist_register_date',
    ];

    public function legal()
    {
        return $this->belongsTo(Legal::class);
    }
}