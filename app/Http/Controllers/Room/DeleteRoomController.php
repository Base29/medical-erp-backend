<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Controller;
use App\Models\Room;

class DeleteRoomController extends Controller
{
    public function __invoke($id)
    {
        // Check if the room exists with the provided $id
        $room = Room::find($id);

        if (!$room) {
            return response([
                'success' => false,
                'message' => 'Room not found with the provided id ' . $id,
            ], 404);
        }

        $room->delete();

        return response([
            'success' => true,
            'message' => 'Room deleted successfully',
        ], 200);
    }
}