<?php

namespace App\Models;

use App\Models\InductionChecklist;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends \Spatie\Permission\Models\Role
{
    use HasFactory;

    protected $hidden = [
        'guard_name',
        'created_at',
        'updated_at',
    ];

    public function inductionChecklist()
    {
        return $this->hasMany(InductionChecklist::class);
    }
}