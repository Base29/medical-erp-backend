<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractSummary extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'employee_type',
        'employee_start_date',
        'contract_start_date',
        'working_time_pattern',
        'contracted_hours_per_week',
        'min_leave_entitlement',
        'contract_document',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}