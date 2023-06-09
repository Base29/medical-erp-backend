<?php

namespace App\Http\Controllers\ItPolicy;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\ItPolicy\CreateItPolicyRequest;
use App\Http\Requests\ItPolicy\DeleteItPolicyRequest;
use App\Http\Requests\ItPolicy\FetchSingleItPolicyRequest;
use App\Http\Requests\ItPolicy\SignItPolicyRequest;
use App\Services\ItPolicy\ItPolicyService;
use Exception;

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
            // Logic here
            return $this->itPolicyService->fetchItPolicies();

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
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

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
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

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Sign It Policy
    public function sign(SignItPolicyRequest $request)
    {
        try {
            // Logic here
            return $this->itPolicyService->signItPolicies($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}