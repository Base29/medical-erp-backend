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

        try {

            // Get Practice
            $practice = Practice::where('id', $request->practice)->with('rooms')->firstOrFail();

            // Check if the room with the same name already exists within the practice
            $roomExists = $practice->rooms->contains('name', $request->name);

            if ($roomExists) {
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

            return Response::success(['room' => $room->with('practice')->latest()->firstOrFail()]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for deleting room
    public function delete($id)
    {

        try {
            // Check if the room exists with the provided $id
            $room = Room::findOrFail($id);

            if (!$room) {
                return Response::fail([
                    'message' => ResponseMessage::notFound('Room', $id, false),
                    'code' => 404,
                ]);
            }

            $room->delete();

            return Response::success(['message' => ResponseMessage::deleteSuccess('Room')]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for fetching rooms
    public function fetch(FetchRoomsRequest $request)
    {

        try {

            if ($request->has('practice')) {

                // Get Practice
                $practice = Practice::where('id', $request->practice)->firstOrFail();

                // Check if the user is assigned to $practice
                $belongsToPractice = $practice->users->contains('id', auth()->user()->id);

                if (!$belongsToPractice) {
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

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update room
    public function update(UpdateRoomRequest $request)
    {

        try {

            // Allowed fields when updating a task
            $allowedFields = [
                'status',
                'active',
            ];

            // Checking if the $request doesn't contain any of the allowed fields
            if (!$request->hasAny($allowedFields)) {
                return Response::fail([
                    'message' => ResponseMessage::allowedFields($allowedFields),
                    'code' => 400,
                ]);
            }

            // Get Room
            $room = Room::findOrFail($request->room);

            // Update Room
            $roomUpdated = $this->updateRoom($request->all(), $room);

            if ($roomUpdated) {
                return Response::success(['room' => $room]);
            }

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Helper function for updating fields for the room sent through request
    private function updateRoom($fields, $room)
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
