<?php

namespace App\Http\Controllers\Interview;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdhocQuestion\CreateAdhocQuestionRequest;
use App\Http\Requests\CandidateQuestion\CreateCandidateQuestionRequest;
use App\Http\Requests\InterviewSchedule\CreateInterviewScheduleRequest;
use App\Http\Requests\InterviewSchedule\DeleteInterviewScheduleRequest;
use App\Http\Requests\InterviewSchedule\UpdateInterviewScheduleRequest;
use App\Http\Requests\Interview\CreateInterviewAnswerRequest;
use App\Http\Requests\Interview\FetchInterviewSchedulesRequest;
use App\Http\Requests\Interview\FetchInterviewsRequest;
use App\Http\Requests\Interview\FetchSingleInterviewRequest;
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
            return $this->interviewService->fetchAllInterviews($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch practice interview schedules
    public function upcomingInterviews(FetchInterviewSchedulesRequest $request)
    {
        try {
            // Fetch practice interview schedules
            return $this->interviewService->fetchUpcomingInterviewSchedules($request);

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

    // Update
    public function update(UpdateInterviewScheduleRequest $request)
    {
        try {
            // Update interview schedule
            return $this->interviewService->updateInterviewSchedule($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete
    public function delete(DeleteInterviewScheduleRequest $request)
    {
        try {
            // Delete interview service
            return $this->interviewService->deleteInterviewSchedule($request);
        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Past Interviews
    public function pastInterviews(FetchInterviewsRequest $request)
    {
        try {
            // Past interview service
            return $this->interviewService->fetchPastInterviews($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Store interview answers
    public function interviewAnswer(CreateInterviewAnswerRequest $request)
    {
        try {
            // Store interview answers
            return $this->interviewService->storeInterviewAnswer($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Create Adhoc Questions
    public function adhocQuestions(CreateAdhocQuestionRequest $request)
    {
        try {
            // Logic here
            return $this->interviewService->createAdhocQuestions($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Create candidate question
    public function candidateQuestions(CreateCandidateQuestionRequest $request)
    {
        try {
            // Create candidate questions
            return $this->interviewService->createCandidateQuestions($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single interview
    public function singleInterview(FetchSingleInterviewRequest $request)
    {
        try {
            // Fetch single interview service
            return $this->interviewService->fetchSingleInterview($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}