<?php

namespace App\Http\Controllers\CheckList;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\CheckList;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CreateCheckListController extends Controller
{
    public function __invoke(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required',
            'room' => 'required|numeric',
            'notes' => 'String',
        ];

        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            // Return error messages against $rules
            return CustomValidation::error_messages($rules, $validator);
        }

        // Check if the room exists
        $room = Room::where('id', $request->room)->first();

        if (!$room) {
            return response([
                'success' => false,
                'message' => 'Room not found with the provided id ' . $request->room,
            ], 404);
        }

        // Check if the checklist with same name exists for the provided room
        $checklist_exists = $room->checkLists->contains('name', $request->name);

        if ($checklist_exists) {
            return response([
                'success' => false,
                'message' => 'Checklist with the provided name ' . $request->name . ' already exists for the room ' . $room->name,
            ], 409);
        }

        // Create Checklist
        $checklist = new CheckList();
        $checklist->name = $request->name;
        $checklist->room_id = $room->id;
        $checklist->notes = $request->notes;
        $checklist->save();

        return response([
            'success' => true,
            'message' => 'Checklist created',
        ], 200);

    }

    private function add_tasks_to_check_list($tasks)
    {
        //
    }
}