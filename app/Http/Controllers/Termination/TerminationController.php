<?php

namespace App\Http\Controllers\Termination;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Termination\CreateTerminationRequest;
use App\Http\Requests\Termination\DeleteTerminationRequest;
use App\Http\Requests\Termination\FetchTerminationRequest;
use App\Http\Requests\Termination\UpdateTerminationRequest;
use App\Services\Termination\TerminationService;
use Exception;

class TerminationController extends Controller
{
    // Local variable
    protected $terminationService;

    // Constructor
    public function __construct(TerminationService $terminationService)
    {
        // Inject Service
        $this->terminationService = $terminationService;
    }

    // Create termination
    public function create(CreateTerminationRequest $request)
    {
        try {

            // Create termination
            return $this->terminationService->createTermination($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch user termination
    public function fetch(FetchTerminationRequest $request)
    {
        try {

            // Fetch termination
            return $this->terminationService->fetchTermination($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update termination
    public function update(UpdateTerminationRequest $request)
    {
        try {

            // Update termination
            return $this->terminationService->updateTermination($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete termination
    public function delete(DeleteTerminationRequest $request)
    {
        try {

            // Delete
            return $this->terminationService->deleteTermination($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}