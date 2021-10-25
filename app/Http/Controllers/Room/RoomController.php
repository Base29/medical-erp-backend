<?php

namespace App\Http\Controllers\Room;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Room\CreateRoomRequest;
use App\Http\Requests\Room\FetchRoomsRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Models\Practice;
use App\Models\Room;

class RoomController extends Controller
{
    // Method for creating room
    public function create(CreateRoomRequest $request)
    {

        // Get Practice
        $practice = Practice::where('id', $request->practice)->with('rooms')->first();

        // Check if the room with the same name already exists within the practice
        $room_exists = $practice->rooms->contains('name', $request->name);

        if ($room_exists) {
            return Response::fail([
                'message' => ResponseMessage::alreadyExists($request->name),
                'code' => 409,
            ]);
        }

        // Create room
        $room = new Room();
        $room->name = $request->name;
        $room->practice_id = $request->practice;
        $room->icon = $request->icon;
        $room->save();

        return Response::success(['room' => $room->with('practice')->latest()->first()]);
    }

    // Method for deleting room
    public function delete($id)
    {
        // Check if the room exists with the provided $id
        $room = Room::find($id);

        if (!$room) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Room', $id, false),
                'code' => 404,
            ]);
        }

        $room->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('Room')]);
    }

    // Method for fetching rooms
    public function fetch(FetchRoomsRequest $request)
    {
        if ($request->has('practice')) {

            // Get Practice
            $practice = Practice::where('id', $request->practice)->first();

            // Check if the user is assigned to $practice
            $belongs_to_practice = $practice->users->contains('id', auth()->user()->id);

            if (!$belongs_to_practice) {
                return Response::fail([
                    'message' => ResponseMessage::customMessage('You cannot view the rooms of the practice ' . $practice->practice_name),
                    'code' => 409,
                ]);
            }

            // Get rooms for the practice
            $rooms = Room::where('practice_id', $request->practice)->with('checklists')->latest()->paginate(10);

            return Response::success(['rooms' => $rooms]);
        }

        // Check if the current user has super_admin role
        if (!auth()->user()->isSuperAdmin()) {
            return Response::fail([
                'message' => ResponseMessage::customMessage('Only super_admin is allowed to view all rooms'),
                'code' => 400,
            ]);
        }

        // Return all rooms
        $rooms = Room::with('checklists')->latest()->paginate(10);

        return Response::success(['rooms' => $rooms]);
    }

    // Update room
    public function update(UpdateRoomRequest $request)
    {
        // Allowed fields when updating a task
        $allowed_fields = [
            'status',
            'active',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowed_fields)) {
            return Response::fail([
                'message' => ResponseMessage::allowedFields($allowed_fields),
                'code' => 400,
            ]);
        }

        // Get Room
        $room = Room::find($request->room);

        // Update Room
        $roomUpdated = $this->update_room($request->all(), $room);

        if ($roomUpdated) {
            return Response::success(['room' => $room]);
        }

    }

    // Helper function for updating fields for the room sent through request
    private function update_room($fields, $room)
    {
        foreach ($fields as $field => $value) {
            if ($field !== 'room') {
                $room->$field = $value;
            }
        }
        $room->save();
        return true;
    }
}