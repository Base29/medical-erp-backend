<?php

namespace App\Http\Controllers\WorkPattern;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkPattern\CreateWorkPatternRequest;
use App\Services\WorkPattern\WorkPatternService;
use Exception;

class WorkPatternController extends Controller
{
    // Local variable
    protected $workPatternService;

    // Constructor
    public function __construct(WorkPatternService $workPatternService)
    {
        // Inject Service
        $this->workPatternService = $workPatternService;
    }

    // Create work pattern
    public function create(CreateWorkPatternRequest $request)
    {
        try {

            // Create work pattern
            return $this->workPatternService->createWorkPattern($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch work patterns
    public function fetch()
    {
        try {
            // Fetch work patterns
            return $this->workPatternService->fetchWorkPatterns();

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete Work Pattern
    public function delete($id)
    {
        try {

            // Delete work pattern
            return $this->workPatternService->deleteWorkPattern($id);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}