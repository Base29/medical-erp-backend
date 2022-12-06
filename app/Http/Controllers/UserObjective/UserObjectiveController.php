<?php

namespace App\Http\Controllers\UserObjective;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserObjective\CreateUserObjectiveRequest;
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
}