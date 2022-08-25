<?php

namespace App\Http\Controllers\TrainingCourse;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingCourse\AssignCourseToUsersRequest;
use App\Http\Requests\TrainingCourse\AssignUserToTrainingCourseRequest;
use App\Http\Requests\TrainingCourse\CreateCourseModuleRequest;
use App\Http\Requests\TrainingCourse\CreateModuleLessonRequest;
use App\Http\Requests\TrainingCourse\CreateTrainingCourseRequest;
use App\Http\Requests\TrainingCourse\DeleteTrainingCourseRequest;
use App\Http\Requests\TrainingCourse\FetchSingleTrainingCourseRequest;
use App\Http\Requests\TrainingCourse\UnassignUserFromTrainingCourseRequest;
use App\Http\Requests\TrainingCourse\UnassignUsersFromCourseRequest;
use App\Http\Requests\TrainingCourse\UpdateTrainingCourseRequest;
use App\Services\TrainingCourse\TrainingCourseService;

class TrainingCourseController extends Controller
{
    // Local variable
    protected $trainingCourseService;

    // Constructor
    public function __construct(TrainingCourseService $trainingCourseService)
    {
        // Inject Service
        $this->trainingCourseService = $trainingCourseService;
    }

    // Create
    public function create(CreateTrainingCourseRequest $request)
    {
        try {
            // Logic here
            return $this->trainingCourseService->createTrainingCourse($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Create course module
    public function createModule(CreateCourseModuleRequest $request)
    {
        try {
            // Logic here
            return $this->trainingCourseService->createCourseModule($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Create Module Lesson
    public function createLesson(CreateModuleLessonRequest $request)
    {
        try {
            // Logic here
            return $this->trainingCourseService->createModuleLesson($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch All Training Courses
    public function fetch()
    {
        try {
            // Logic here
            return $this->trainingCourseService->fetchAllTrainingCourses();

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single course
    public function singleCourse(FetchSingleTrainingCourseRequest $request)
    {
        try {
            // Logic here
            return $this->trainingCourseService->fetchSingleTrainingCourse($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete
    public function delete(DeleteTrainingCourseRequest $request)
    {
        try {
            // Logic here
            return $this->trainingCourseService->deleteTrainingCourse($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update training course
    public function updateCourse(UpdateTrainingCourseRequest $request)
    {
        try {
            // Logic here
            return $this->trainingCourseService->updateTrainingCourse($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Assign course
    public function enrollCourse(AssignUserToTrainingCourseRequest $request)
    {
        try {
            // Logic here
            return $this->trainingCourseService->enrollUserToCourse($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Unroll user
    public function unrollUser(UnassignUserFromTrainingCourseRequest $request)
    {
        try {
            // Logic here
            return $this->trainingCourseService->unrollUserFromCourse($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Assign course to users
    public function assignToUsers(AssignCourseToUsersRequest $request)
    {
        try {
            // Logic here
            return $this->trainingCourseService->assignCourseToUsers($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Unassign course from users
    public function unassignUsers(UnassignUsersFromCourseRequest $request)
    {
        try {
            // Logic here
            return $this->trainingCourseService->unassignUsersFromCourse($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}