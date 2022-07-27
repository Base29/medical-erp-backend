<?php

namespace App\Http\Controllers\TrainingCourse;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
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
}