<?php

namespace App\Http\Controllers\InterviewPolicy;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\InterviewPolicy\CreateInterviewPolicyRequest;
use App\Http\Requests\InterviewPolicy\DeleteInterviewPolicyRequest;
use App\Http\Requests\InterviewPolicy\FetchAllInterviewPolicyRequest;
use App\Http\Requests\InterviewPolicy\FetchSingleInterviewPolicyRequest;
use App\Services\InterviewPolicy\InterviewPolicyService;

class InterviewPolicyController extends Controller
{
    // Local variable
    protected $interviewPolicyService;

    // Constructor
    public function __construct(InterviewPolicyService $interviewPolicyService)
    {
        // Inject Service
        $this->interviewPolicyService = $interviewPolicyService;
    }

    // Create
    public function create(CreateInterviewPolicyRequest $request)
    {
        try {
            // Create service
            return $this->interviewPolicyService->createInterviewPolicy($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch All
    public function fetch()
    {
        try {
            // Fetch all interview policies service
            return $this->interviewPolicyService->fetchAllInterviewPolicies();

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch practice interview policies
    public function fetchAllPracticeInterviewPolicies(FetchAllInterviewPolicyRequest $request)
    {
        try {
            // Fetch all interview policies of a practice
            return $this->interviewPolicyService->fetchPracticeInterviewPolicies($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single
    public function fetchSingle(FetchSingleInterviewPolicyRequest $request)
    {
        try {
            // Fetch single interview policy
            return $this->interviewPolicyService->fetchSingleInterviewPolicy($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete
    public function delete(DeleteInterviewPolicyRequest $request)
    {
        try {
            // Delete interview policy
            return $this->interviewPolicyService->deleteInterviewPolicy($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}