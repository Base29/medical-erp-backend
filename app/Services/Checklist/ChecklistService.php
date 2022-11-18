<?php

/**
 * Checklist Service
 */

namespace App\Services\Checklist;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Models\CheckList;
use App\Models\Room;
use Exception;

class ChecklistService
{
    // Create Checklist
    public function createChecklist($request)
    {
        // Fetch room
        $room = Room::findOrFail($request->room);

        // Check if the checklist with same name exists for the provided room
        $checklistExists = $room->checkLists->contains('name', $request->name);

        if ($checklistExists) {
            throw new Exception(ResponseMessage::alreadyExists('Checklist'), Response::HTTP_CREATED);
        }

        // Create Checklist
        $checklist = new CheckList();
        $checklist->name = $request->name;
        $checklist->room_id = $room->id;
        $checklist->notes = $request->notes;
        $checklist->save();

        // Return checklist
        return $checklist;
    }

    // Fetch checklist
    public function fetchChecklists($request)
    {
        // Get room
        $room = Room::findOrFail($request->room);

        // Return checklists
        return CheckList::where('room_id', $room->id)->with('activeTasks')->latest()->first();
    }
}