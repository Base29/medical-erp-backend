<?php

namespace App\Models;

use App\Models\CheckList;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'frequency',
        'status',
        'comment',
        'reason',
        'check_list_id',
    ];

    public function checkList()
    {
        return $this->belongsTo(CheckList::class);
    }
}