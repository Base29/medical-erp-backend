<?php

namespace App\Http\Controllers\Room;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\Practice;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    // Method for creating room
    public function create(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required',
            'practice' => 'required|numeric',
        ];

        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            // Return error messages against $rules
            return CustomValidation::error_messages($rules, $validator);
        }

        // Check if the practice exists
        $practice = Practice::where('id', $request->practice)->with('rooms')->first();

        if (!$practice) {
            return response([
                'success' => false,
                'message' => 'Practice not found by the provided id ' . $request->practice,
            ], 404);
        }

        // Check if the room with the same name already exists within the practice
        $room_exists = $practice->rooms->contains('name', $request->name);

        if ($room_exists) {
            return response([
                'success' => false,
                'message' => 'Room already exists with the provided name ' . $request->name . ' in practice ' . $practice->practice_name,
            ], 409);
        }

        // Create room
        $room = new Room();
        $room->name = $request->name;
        $room->practice_id = $request->practice;
        $room->save();

        return response([
            'success' => true,
            'message' => 'Room ' . $room->name . ' created successfully for practice ' . $practice->practice_name,
        ], 200);
    }

    // Method for deleting room
    public function delete($id)
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

    // Method for fetching rooms
    public function fetch(Request $request)
    {
        if ($request->has('practice')) {
            // Validation rules
            $rules = [
                'practice' => 'required|numeric',
            ];

            // Validating params in request
            $validator = Validator::make($request->all(), $rules);

            // If validation fails
            if ($validator->fails()) {
                // Return error messages against $rules
                return CustomValidation::error_messages($rules, $validator);
            }

            //Check if the practice exists
            $practice = Practice::find($request->practice);

            if (!$practice) {
                return response([
                    'success' => false,
                    'message' => 'Practice not found with the provided id ' . $request->practice,
                ], 404);
            }
            // Get rooms for the practice
            $rooms = Room::where('practice_id', $request->practice)->paginate(10);

            return response([
                'success' => true,
                'rooms' => $rooms,
            ], 200);
        }

        $rooms = Room::paginate(10);

        return response([
            'success' => true,
            'rooms' => $rooms,
        ]);
    }

    public function update(Request $request)
    {
        if (!$request->has('status') && !$request->has('active')) {
            return response([
                'success' => false,
                'message' => 'Key status or active is required',
            ], 400);
        }

        // Validation rules
        $rules = [
            'status' => 'boolean',
            'active' => 'boolean',
            'room' => 'required|numeric',
        ];

        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            // Return error messages against $rules
            return CustomValidation::error_messages($rules, $validator);
        }

        // Check if the room exists
        $room = Room::find($request->room);

        if (!$room) {
            return response([
                'success' => false,
                'message' => 'Room with ID ' . $request->room . ' not found',
            ], 404);
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

        return response([
            'success' => true,
            'room' => $room,
        ], 200);
    }
}