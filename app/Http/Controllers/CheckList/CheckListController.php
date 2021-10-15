<?php

namespace App\Http\Controllers\CheckList;

use App\Helpers\CustomValidation;
use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Models\CheckList;
use App\Models\Room;
use Illuminate\Http\Request;

class CheckListController extends Controller
{
    // Method for creating check list
    public function create(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required',
            'room' => 'required|numeric',
            'notes' => 'String',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Check if the room exists
        $room = Room::where('id', $request->room)->first();

        if (!$room) {
            return Response::fail([
                'message' => 'Room not found with the provided id ' . $request->room,
                'code' => 404,
            ]);
        }

        // Check if the checklist with same name exists for the provided room
        $checklist_exists = $room->checkLists->contains('name', $request->name);

        if ($checklist_exists) {
            return Response::fail([
                'message' => 'Checklist with the provided name ' . $request->name . ' already exists for the room ' . $room->name,
                'code' => 409,
            ]);
        }

        // Create Checklist
        $checklist = new CheckList();
        $checklist->name = $request->name;
        $checklist->room_id = $room->id;
        $checklist->notes = $request->notes;
        $checklist->save();

        return Response::success(['checklist' => $checklist]);
    }

    public function fetch(Request $request)
    {
        // Validation rules
        $rules = [
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
                'message' => 'Room with the ID ' . $request->room . ' not found',
                'code' => 404,
            ]);
        }

        // Fetch checklists for the room
        $checklists = CheckList::where('room_id', $room->id)->with('tasks')->first();

        return Response::success(['checklists' => $checklists]);
    }
}