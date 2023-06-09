<?php

namespace App\Http\Controllers\AppraisalPolicy;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\AppraisalPolicy\CreateAppraisalPolicyRequest;
use App\Http\Requests\AppraisalPolicy\DeleteAppraisalPolicyRequest;
use App\Http\Requests\AppraisalPolicy\FetchAllAppraisalPoliciesRequest;
use App\Http\Requests\AppraisalPolicy\FetchRoleAppraisalPolicyRequest;
use App\Http\Requests\AppraisalPolicy\FetchSingleAppraisalPolicyRequest;
use App\Http\Requests\AppraisalPolicy\UpdateAppraisalPolicyQuestionRequest;
use App\Http\Requests\AppraisalPolicy\UpdateAppraisalPolicyRequest;
use App\Services\Appraisal\AppraisalService;
use Exception;

class AppraisalPolicyController extends Controller
{
    // Local variable
    protected $appraisalService;

    // Constructor
    public function __construct(AppraisalService $appraisalService)
    {
        // Inject Service
        $this->appraisalService = $appraisalService;
    }

    // Create
    public function create(CreateAppraisalPolicyRequest $request)
    {
        try {
            // Create service
            return $this->appraisalService->createAppraisalPolicy($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch All
    public function fetch()
    {
        try {
            // Fetch all appraisal policies service
            return $this->appraisalService->fetchAllAppraisalPolicies();

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch practice appraisal policies
    public function fetchAllPracticeAppraisalPolicies(FetchAllAppraisalPoliciesRequest $request)
    {
        try {
            // Fetch all appraisal policies of a practice
            return $this->appraisalService->fetchPracticeAppraisalPolicies($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single
    public function fetchSingle(FetchSingleAppraisalPolicyRequest $request)
    {
        try {
            // Fetch single appraisal policy
            return $this->appraisalService->fetchSingleAppraisalPolicy($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete
    public function delete(DeleteAppraisalPolicyRequest $request)
    {
        try {
            // Delete appraisal policy
            return $this->appraisalService->deleteAppraisalPolicy($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update
    public function update(UpdateAppraisalPolicyRequest $request)
    {
        try {
            // Update appraisal Policy
            return $this->appraisalService->updateAppraisalPolicy($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update appraisal policy question
    public function updateAppraisalQuestion(UpdateAppraisalPolicyQuestionRequest $request)
    {
        try {
            // Update appraisal policy question
            return $this->appraisalService->updateQuestion($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch role appraisal policy
    public function fetchRolePolicy(FetchRoleAppraisalPolicyRequest $request)
    {
        try {
            // Logic here
            return $this->appraisalService->fetchRoleAppraisalPolicy($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}