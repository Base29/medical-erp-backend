<?php

namespace App\Http\Controllers\User;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Locum\UpdateUserLocumStatusRequest;
use App\Http\Requests\User\CourseProgressRequest;
use App\Http\Requests\User\CreateEndOfModuleExamRequest;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\FetchSingleEnrolledCourseRequest;
use App\Http\Requests\User\FetchSingleUserRequest;
use App\Http\Requests\User\FetchUserSessionInvitesRequest;
use App\Http\Requests\User\FetchUsersRequest;
use App\Http\Requests\User\FilterUsersRequest;
use App\Http\Requests\User\HireCandidateRequest;
use App\Http\Requests\User\LessonProgressRequest;
use App\Http\Requests\User\ModuleProgressRequest;
use App\Http\Requests\User\SearchCandidateProfilesRequest;
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

    // Record lesson progress
    public function recordLesson(LessonProgressRequest $request)
    {
        try {
            // Logic here
            return $this->userService->recordLessonProgress($request);
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

    // Record module progress
    public function recordModule(ModuleProgressRequest $request)
    {
        try {
            // Logic here
            return $this->userService->recordModuleProgress($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Record course progress
    public function recordCourse(CourseProgressRequest $request)
    {
        try {
            // Logic here
            return $this->userService->recordCourseProgress($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // End of module exam
    public function endOfModuleExam(CreateEndOfModuleExamRequest $request)
    {
        try {
            // Logic here
            return $this->userService->createEndOfModuleExam($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Single enrolled course
    public function singleEnrolledCourse(FetchSingleEnrolledCourseRequest $request)
    {
        try {
            // Logic here
            return $this->userService->fetchSingleEnrolledCourse($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch employees
    public function employees()
    {
        try {
            // Logic here
            return $this->userService->fetchEmployees();

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Search Profiles
    public function searchProfiles(SearchCandidateProfilesRequest $request)
    {
        try {
            // Logic here
            return $this->userService->searchCandidateProfiles($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Make user as locum
    public function updateLocumStatus(UpdateUserLocumStatusRequest $request)
    {
        try {
            // Logic here
            return $this->userService->updateUserLocumStatus($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function filter(FilterUsersRequest $request)
    {
        try {
            // Logic here
            return $this->userService->filterUsers($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch Invites
    public function fetchUserInvites(FetchUserSessionInvitesRequest $request)
    {
        try {
            // Logic here
            return $this->userService->fetchUserSessionInvites($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}