<?php

namespace App\Http\Controllers\CheckList;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Checklist\CreateChecklistRequest;
use App\Http\Requests\Checklist\FetchChecklistRequest;
use App\Models\CheckList;
use App\Models\Room;

class CheckListController extends Controller
{
    // Method for creating check list
    public function create(CreateChecklistRequest $request)
    {
        try {

            // Fetch room
            $room = Room::where('id', $request->room)->first();

            // Check if the checklist with same name exists for the provided room
            $checklistExists = $room->checkLists->contains('name', $request->name);

            if ($checklistExists) {
                return Response::fail([
                    'message' => ResponseMessage::alreadyExists('Checklist'),
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

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function fetch(FetchChecklistRequest $request)
    {
        try {

            $checklists = CheckList::where('room_id', $request->room)->with('tasks')->latest()->first();

            return Response::success(['checklists' => $checklists]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}