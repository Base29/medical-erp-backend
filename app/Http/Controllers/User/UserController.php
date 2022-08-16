<?php

namespace App\Http\Controllers\User;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\FetchSingleUserRequest;
use App\Http\Requests\User\FetchUsersRequest;
use App\Http\Requests\User\HireCandidateRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\User\UserService;

class UserController extends Controller
{
    // Local variable
    protected $userService;

    // Constructor
    public function __construct(UserService $userService)
    {
        // Inject Service
        $this->userService = $userService;
    }

    // Method for creating user
    public function create(CreateUserRequest $request)
    {
        try {

            // Create user
            return $this->userService->createUser($request);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for deleting user
    public function delete($id)
    {
        try {

            // Delete service
            return $this->userService->deleteUser($id);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for fetching users
    public function fetch(FetchUsersRequest $request)
    {
        try {
            // Fetch users
            return $this->userService->fetchUsers($request);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for updating user
    public function update(UpdateUserRequest $request)
    {
        try {

            // Update user profile
            return $this->userService->updateUser($request);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch individual user profile
    public function me()
    {
        try {

            // Fetch me
            return $this->userService->me();

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single user
    public function fetchSingle(FetchSingleUserRequest $request)
    {
        try {
            // Fetch single user service
            return $this->userService->fetchSingleUser($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Generate candidate password
    public function hire(HireCandidateRequest $request)
    {
        try {
            // Logic here
            return $this->userService->hireCandidate($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch user courses
    public function userTrainingCourses()
    {
        try {

            // Logic here
            return $this->userService->fetchUserTrainingCourses();

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}