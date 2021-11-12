<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PositionSummary extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'job_title',
        'contract_type',
        'department',
        'reports_to',
        'probation_end_date',
        'notice_period',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}