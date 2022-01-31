<?php

namespace App\Http\Controllers\InductionResult;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\InductionResult\CreateInductionResultRequest;
use App\Services\InductionResult\InductionResultService;

class InductionResultController extends Controller
{

    // Local variable
    protected $inductionResultService;

    // Constructor
    public function __construct(InductionResultService $inductionResultService)
    {
        // Inject service
        $this->inductionResultService = $inductionResultService;
    }

    // Create induction result
    public function create(CreateInductionResultRequest $request)
    {
        try {

            // Create induction results
            $inductionResults = $this->inductionResultService->createInductionResult($request);

            // Return success response
            return Response::success([
                'induction-results' => $inductionResults,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}