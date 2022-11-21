<?php

namespace App\Http\Controllers\WorkTiming;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkTiming\FetchWorkTimingRequest;
use App\Http\Requests\WorkTiming\UpdateWorkTimingRequest;
use App\Services\WorkTiming\WorkTimingService;
use Exception;

class WorkTimingController extends Controller
{
    // Local variable
    protected $workTimingService;

    // Constructor
    public function __construct(WorkTimingService $workTimingService)
    {
        // Inject Service
        $this->workTimingService = $workTimingService;
    }
    // Update work timing
    public function update(UpdateWorkTimingRequest $request)
    {
        try {

            // Update work timing
            return $this->workTimingService->updateWorkTiming($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch work timings for a work pattern
    public function fetch(FetchWorkTimingRequest $request)
    {
        try {

            // Fetch work timings
            return $this->workTimingService->fetchWorkTimings($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}