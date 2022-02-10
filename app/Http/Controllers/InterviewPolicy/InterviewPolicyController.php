<?php

namespace App\Http\Controllers\InterviewPolicy;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\InterviewPolicy\CreateInterviewPolicyRequest;
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
}