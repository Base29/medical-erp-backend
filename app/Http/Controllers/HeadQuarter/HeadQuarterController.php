<?php

namespace App\Http\Controllers\HeadQuarter;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\HeadQuarter\ProcessHiringRequest;
use App\Services\HeadQuarter\HeadQuarterService;

class HeadQuarterController extends Controller
{

    // Local variable
    protected $headQuarterService;

    // Constructor
    public function __construct(HeadQuarterService $headQuarterService)
    {
        // Inject service
        $this->headQuarterService = $headQuarterService;
    }

    // Process hiring request
    public function processHiringRequest(ProcessHiringRequest $request)
    {
        try {
            // Process hiring request
            $hiringRequest = $this->headQuarterService->processHiringRequest($request);

            // Return success response
            return Response::success([
                'hiring-request' => $hiringRequest,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch all offers within the system
    public function fetchOffers()
    {
        try {
            // Fetch Offers
            return $this->headQuarterService->fetchAllOffers();

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}