<?php

namespace App\Http\Controllers\TrainingCourse;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingCourse\CreateCourseModuleRequest;
use App\Http\Requests\TrainingCourse\CreateModuleLessonRequest;
use App\Http\Requests\TrainingCourse\CreateTrainingCourseRequest;
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
}