<?php

namespace App\Http\Controllers\Practice;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Practice\AssignPracticeToUserRequest;
use App\Http\Requests\Practice\CreatePracticeRequest;
use App\Http\Requests\Practice\RevokePracticeForUserRequest;
use App\Services\Practice\PracticeService;
use Exception;

class PracticeController extends Controller
{
    // Local variable
    protected $practiceService;

    // Constructor
    public function __construct(PracticeService $practiceService)
    {
        // Inject service
        $this->practiceService = $practiceService;
    }

    // Method for creating practices
    public function create(CreatePracticeRequest $request)
    {

        try {

            // Create practice
            return $this->practiceService->createPractice($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }

    }

    // Method for deleting practice
    public function delete($id)
    {
        try {

            // Delete practice
            return $this->practiceService->deletePractice($id);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }

    }

    // Method for fetching practices
    public function fetch()
    {
        try {

            // Fetch practices
            return $this->practiceService->fetchPractices();

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for assigning user to practice
    public function assignToUser(AssignPracticeToUserRequest $request)
    {

        try {

            // Assign user to practice
            return $this->practiceService->assignUserToPractice($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }

    }

    // Method for revoking user from practice
    public function revokeForUser(RevokePracticeForUserRequest $request)
    {

        try {

            // Revoke user from practice
            return $this->practiceService->revokeUserFromPractice($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}