<?php

namespace App\Models;

use App\Models\Room;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckList extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'room_id',
        'notes',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

}