<?php

namespace App\Http\Controllers\Policy;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Policy\CreatePolicyRequest;
use App\Models\Policy;
use App\Services\Policy\PolicyService;

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

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function create(CreatePolicyRequest $request)
    {

        try {
            // Create policy
            return $this->policyService->createPolicy($request);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function delete($id)
    {

        try {

            // Delete policy
            return $this->policyService->deletePolicy($id);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}