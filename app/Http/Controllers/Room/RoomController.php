<?php

namespace App\Http\Controllers\Room;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Room\CreateRoomRequest;
use App\Http\Requests\Room\FetchRoomsRequest;
use App\Http\Requests\Room\UpdateRoomRequest;
use App\Services\Room\RoomService;
use Exception;

class RoomController extends Controller
{
    // Local variable
    protected $roomService;

    // Constructor
    public function __construct(RoomService $roomService)
    {
        // Inject Service
        $this->roomService = $roomService;
    }

    // Method for creating room
    public function create(CreateRoomRequest $request)
    {

        try {

            // Create room
            return $this->roomService->createRoom($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for deleting room
    public function delete($id)
    {

        try {

            // Delete room service
            return $this->roomService->deleteRoom($id);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for fetching rooms
    public function fetch(FetchRoomsRequest $request)
    {

        try {

            // Fetch rooms
            return $this->roomService->fetchRooms($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update room
    public function update(UpdateRoomRequest $request)
    {

        try {

            // Update room
            return $this->roomService->updateRoom($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}