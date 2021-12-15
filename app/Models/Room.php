<?php

namespace App\Models;

use App\Models\Practice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'practice_id',
    ];

    public function practice()
    {
        return $this->belongsTo(Practice::class);
    }

    public function checklists()
    {
        return $this->hasMany(CheckList::class);
    }

    public function updateRoomStatus($id)
    {
        // Fetch room
        $room = Room::where('id', $id)->first();

        // Updating status of the room to 0
        $room->status = 0;
        $room->save();
    }
}