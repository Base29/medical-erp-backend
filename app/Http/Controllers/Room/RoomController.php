<?php

namespace App\Http\Controllers\Room;

use App\Helpers\CustomValidation;
use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Models\Practice;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    // Method for creating room
    public function create(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required',
            'practice' => 'required|numeric',
            'icon' => 'required',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Check if the practice exists
        $practice = Practice::where('id', $request->practice)->with('rooms')->first();

        if (!$practice) {
            return Response::fail([
                'message' => 'Practice not found by the provided id ' . $request->practice,
                'code' => 404,
            ]);
        }

        // Check if the room with the same name already exists within the practice
        $room_exists = $practice->rooms->contains('name', $request->name);

        if ($room_exists) {
            return Response::fail([
                'message' => 'Room already exists with the provided name ' . $request->name . ' in practice ' . $practice->practice_name,
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
                'message' => 'Room with ID ' . $id . ' not found',
                'code' => 404,
            ]);
        }

        $room->delete();

        return Response::success(['message' => 'Room deleted successfully']);
    }

    // Method for fetching rooms
    public function fetch(Request $request)
    {
        if ($request->has('practice')) {
            // Validation rules
            $rules = [
                'practice' => 'required|numeric',
            ];

            // Validation errors
            $request_errors = CustomValidation::validate_request($rules, $request);

            // Return errors
            if ($request_errors) {
                return $request_errors;
            }

            //Check if the practice exists
            $practice = Practice::where('id', $request->practice)->first();

            if (!$practice) {
                return Response::fail([
                    'message' => 'Practice with ID ' . $request->practice . ' not found',
                    'code' => 404,
                ]);
            }

            // Check if the user is assigned to $practice
            $belongs_to_practice = $practice->users->contains('id', auth()->user()->id);

            if (!$belongs_to_practice) {
                return Response::fail([
                    'message' => 'You cannot view the rooms of the practice ' . $practice->practice_name,
                    'code' => 409,
                ]);
            }

            // Get rooms for the practice
            $rooms = Room::where('practice_id', $request->practice)->with('checklists')->latest()->paginate(10);

            return Response::success(['rooms' => $rooms]);
        }

        //TODO: Allow only Admins to fetch all the rooms regardless of which practice the room belongs to.
        $rooms = Room::with('checklists')->latest()->paginate(10);

        return Response::success(['rooms' => $rooms]);
    }

    public function update(Request $request)
    {
        // Allowed fields when updating a task
        $allowed_fields = [
            'status',
            'active',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowed_fields)) {
            return Response::fail([
                'message' => 'Update request should contain any of the allowed fields ' . implode("|", $allowed_fields),
                'code' => 400,
            ]);
        }

        // Validation rules
        $rules = [
            'status' => 'boolean',
            'active' => 'boolean',
            'room' => 'required|numeric',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Check if the room exists
        $room = Room::find($request->room);

        if (!$room) {
            return Response::fail([
                'message' => 'Room with ID ' . $request->room . ' not found',
                'code' => 404,
            ]);
        }

        // If status key is being sent
        if ($request->has('status')) {
            $room->status = $request->status;
        }

        // If active key is being sent
        if ($request->has('active')) {
            $room->active = $request->active;
        }

        $room->save();

        return Response::success(['room' => $room]);
    }
}