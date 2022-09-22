<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LocumInvoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [];

    public function locum()
    {
        return $this->belongsTo(User::class, 'locum', 'id');
    }

    public function session()
    {
        return $this->belongsTo(LocumSession::class, 'session', 'id');
    }
}