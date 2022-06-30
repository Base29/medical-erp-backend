<?php

namespace App\Http\Controllers\ItPolicy;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\ItPolicy\CreateItPolicyRequest;
use App\Http\Requests\ItPolicy\DeleteItPolicyRequest;
use App\Http\Requests\ItPolicy\FetchSingleItPolicyRequest;
use App\Services\ItPolicy\ItPolicyService;

class ItPolicyController extends Controller
{
    // Local variable
    protected $itPolicyService;

    // Constructor
    public function __construct(ItPolicyService $itPolicyService)
    {
        // Inject Service
        $this->itPolicyService = $itPolicyService;
    }

    // Create It Policy
    public function create(CreateItPolicyRequest $request)
    {
        try {
            // Logic here
            return $this->itPolicyService->createItPolicy($request);

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
            // Logic here
            return $this->itPolicyService->fetchItPolicies();

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete
    public function delete(DeleteItPolicyRequest $request)
    {
        try {
            // Logic here
            return $this->itPolicyService->deleteItPolicy($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch Single
    public function fetchSingle(FetchSingleItPolicyRequest $request)
    {
        try {
            // Logic here
            return $this->itPolicyService->fetchSingleItPolicy($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}