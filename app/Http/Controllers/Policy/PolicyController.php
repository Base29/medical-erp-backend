<?php

namespace App\Http\Controllers\Policy;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Policy\CreatePolicyRequest;
use App\Http\Requests\Policy\FetchSinglePolicyRequest;
use App\Services\Policy\PolicyService;
use Exception;

class PolicyController extends Controller
{
    // Local variable
    protected $policyService;

    // Constructor
    public function __construct(PolicyService $policyService)
    {
        // Inject service
        $this->policyService = $policyService;
    }

    // Method for fetching policies
    public function fetch()
    {
        try {

            // Fetch policies
            return $this->policyService->fetchPolicies();

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function create(CreatePolicyRequest $request)
    {

        try {
            // Create policy
            return $this->policyService->createPolicy($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function delete($id)
    {

        try {

            // Delete policy
            return $this->policyService->deletePolicy($id);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Single policy
    public function single(FetchSinglePolicyRequest $request)
    {
        try {
            // Logic here
            return $this->policyService->fetchSinglePolicy($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}