<?php

namespace App\Http\Controllers\Interview;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\InterviewSchedule\CreateInterviewScheduleRequest;
use App\Http\Requests\Interview\FetchInterviewSchedulesRequest;
use App\Http\Requests\Interview\FetchInterviewsRequest;
use App\Services\Interview\InterviewService;

class InterviewController extends Controller
{
    // Local variable
    protected $interviewService;

    // Constructor
    public function __construct(InterviewService $interviewService)
    {
        // Inject Service
        $this->interviewService = $interviewService;
    }

    // Fetch interviews of practice
    public function fetch(FetchInterviewsRequest $request)
    {
        try {
            // Fetch practice interview
            return $this->interviewService->fetchPracticeInterviews($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch practice interview schedules
    public function interviewSchedules(FetchInterviewSchedulesRequest $request)
    {
        try {
            // Fetch practice interview schedules
            return $this->interviewService->fetchInterviewSchedules($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Create
    public function create(CreateInterviewScheduleRequest $request)
    {
        try {
            // Create interview schedule service
            return $this->interviewService->createInterviewSchedule($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}