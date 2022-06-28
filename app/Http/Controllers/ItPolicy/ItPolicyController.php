<?php

namespace App\Http\Controllers\ItPolicy;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\ItPolicy\CreateItPolicyRequest;
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
}