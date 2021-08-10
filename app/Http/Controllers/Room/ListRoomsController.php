<?php

namespace App\Http\Controllers\Room;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\Practice;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ListRoomsController extends Controller
{
    public function __invoke(Request $request)
    {
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
}