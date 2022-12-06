<?php

namespace App\Http\Controllers\UserObjective;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserObjective\CreateUserObjectiveRequest;
use App\Http\Requests\UserObjective\DeleteUserObjectiveRequest;
use App\Http\Requests\UserObjective\UpdateUserObjectiveRequest;
use App\Services\UserObjective\UserObjectiveService;
use Exception;

class UserObjectiveController extends Controller
{
    // Local variable
    protected $userObjectiveService;

    // Constructor
    public function __construct(UserObjectiveService $userObjectiveService)
    {
        // Inject Service
        $this->userObjectiveService = $userObjectiveService;
    }

    // Create user objective
    public function create(CreateUserObjectiveRequest $request)
    {
        try {
            // Logic here
            return $this->userObjectiveService->createUserObjective($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update

    public function update(UpdateUserObjectiveRequest $request)
    {
        try {
            // Logic here
            return $this->userObjectiveService->updateUserObjective($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete
    public function delete(DeleteUserObjectiveRequest $request)
    {
        try {
            // Logic here
            return $this->userObjectiveService->deleteUserObjective($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}