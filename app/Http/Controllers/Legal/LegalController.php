<?php

namespace App\Http\Controllers\Legal;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Legal\CreateLegalRequest;
use App\Http\Requests\Legal\DeleteLegalRequest;
use App\Http\Requests\Legal\FetchLegalRequest;
use App\Http\Requests\Legal\UpdateLegalRequest;
use App\Services\Legal\LegalService;

class LegalController extends Controller
{

    // Local variable
    protected $legalService;

    // Constructor
    public function __construct(LegalService $legalService)
    {
        // Inject Service
        $this->legalService = $legalService;
    }

    // Create Legal
    public function create(CreateLegalRequest $request)
    {
        try {
            // Create legal
            $legal = $this->legalService->createLegal($request);

            // Return success response
            return Response::success([
                'legal' => $legal,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch user's legal
    public function fetch(FetchLegalRequest $request)
    {
        try {

            // Fetch single legal
            $legal = $this->legalService->fetchSingleLegal($request);

            // Return success response
            return Response::success([
                'legal' => $legal,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete Legal
    public function delete(DeleteLegalRequest $request)
    {
        try {
            // Delete legal
            $this->legalService->deleteLegal($request);

            // Return success response
            return Response::success([
                'message' => ResponseMessage::deleteSuccess('Legal'),
            ]);
        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update Legal
    public function update(UpdateLegalRequest $request)
    {
        try {

            // Update legal
            $legal = $this->legalService->updateLegal($request);

            // Return success response
            return Response::success([
                'legal' => $legal,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

}