<?php

namespace App\Http\Controllers\Reason;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reason\CreateReasonRequest;
use App\Services\Reason\ReasonService;
use Exception;

class ReasonController extends Controller
{

    // Local variable
    protected $reasonService;

    // Constructor
    public function __construct(ReasonService $reasonService)
    {
        // Inject service
        $this->reasonService = $reasonService;
    }

    // Create Reason
    public function create(CreateReasonRequest $request)
    {
        try {

            // Create reason
            return $this->reasonService->createReason($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch Reasons
    public function fetch()
    {
        try {

            // Fetch reasons
            return $this->reasonService->fetchReasons();

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete Reasons
    public function delete($id)
    {
        try {

            // Delete reason
            return $this->reasonService->deleteReason($id);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}