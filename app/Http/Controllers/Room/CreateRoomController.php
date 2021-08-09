<?php

namespace App\Http\Controllers\Room;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\Practice;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateRoomController extends Controller
{
    public function __invoke(Request $request)
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
}