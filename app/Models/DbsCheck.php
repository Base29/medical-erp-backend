<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DbsCheck extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'dbs_required',
        'self_declaration_completed',
        'self_declaration_certificate',
        'dbs_conducted',
        'dbs_conducted_date',
        'follow_up_date',
        'dbs_certificate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}