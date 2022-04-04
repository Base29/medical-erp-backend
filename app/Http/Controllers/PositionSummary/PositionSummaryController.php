<?php

namespace App\Http\Controllers\PositionSummary;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\PositionSummary\CreatePositionSummaryRequest;
use App\Http\Requests\PositionSummary\FetchSinglePositionSummaryRequest;
use App\Http\Requests\PositionSummary\UpdatePositionSummaryRequest;
use App\Models\PositionSummary;
use App\Services\PositionSummary\PositionSummaryService;

class PositionSummaryController extends Controller
{
    // Local variable
    protected $positionSummaryService;

    // Constructor
    public function __construct(PositionSummaryService $positionSummaryService)
    {
        // Inject service
        $this->positionSummaryService = $positionSummaryService;
    }

    // Create position summary
    public function create(CreatePositionSummaryRequest $request)
    {
        try {

            // Create position summary
            return $this->positionSummaryService->createPositionSummary($request);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update position summary
    public function update(UpdatePositionSummaryRequest $request)
    {
        try {

            // Update position summary
            return $this->positionSummaryService->updatePositionSummary($request);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single position summary
    public function fetchSingle(FetchSinglePositionSummaryRequest $request)
    {
        try {

            // Fetch single position summary
            return $this->positionSummaryService->fetchSinglePositionSummary($request);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete position summary
    public function delete($id)
    {
        try {

            // Delete position summary
            return $this->positionSummaryService->deletePositionSummary($id);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}