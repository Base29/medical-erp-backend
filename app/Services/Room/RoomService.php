<?php
namespace App\Services\Room;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\Practice;
use App\Models\Room;

class RoomService
{
    // Create room
    public function createRoom($request)
    {
        // Get Practice
        $practice = Practice::where('id', $request->practice)->with('rooms')->firstOrFail();

        // Check if the room with the same name already exists within the practice
        $roomExists = $practice->rooms->contains('name', $request->name);

        if ($roomExists) {
            throw new \Exception(ResponseMessage::alreadyExists($request->name));
        }

        // Create room
        $room = new Room();
        $room->name = $request->name;
        $room->practice_id = $request->practice;
        $room->icon = $request->icon;
        $room->save();

        return Response::success(['room' => $room->with('practice')->latest()->firstOrFail()]);
    }

    // Delete room
    public function deleteRoom($id)
    {
        // Check if the room exists with the provided $id
        $room = Room::findOrFail($id);

        if (!$room) {
            throw new \Exception(ResponseMessage::notFound('Room', $id, false));
        }

        $room->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('Room')]);
    }

    // Fetch rooms
    public function fetchRooms($request)
    {
        if ($request->has('practice')) {

            // Get Practice
            $practice = Practice::where('id', $request->practice)->firstOrFail();

            // Check if the user is assigned to $practice
            $belongsToPractice = $practice->users->contains('id', auth()->user()->id);

            if (!$belongsToPractice) {
                throw new \Exception(ResponseMessage::customMessage('You cannot view the rooms of the practice ' . $practice->practice_name));
            }

            // Get rooms for the practice
            $rooms = Room::where('practice_id', $request->practice)->with('checklists')->latest()->paginate(10);

            return Response::success(['rooms' => $rooms]);
        }

        // Check if the current user has super_admin role
        if (!auth()->user()->isSuperAdmin()) {
            throw new \Exception(ResponseMessage::customMessage('Only super_admin is allowed to view all rooms'));
        }

        // Return all rooms
        $rooms = Room::with('checklists')->latest()->paginate(10);

        return Response::success(['rooms' => $rooms]);
    }

    // Update room
    public function updateRoom($request)
    {
        // Allowed fields when updating a task
        $allowedFields = [
            'status',
            'active',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new \Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Get Room
        $room = Room::findOrFail($request->room);

        // Update Room
        UpdateService::updateModel($room, $request->validated(), 'room');

        // Return success response
        return Response::success(['room' => $room]);

    }
}
