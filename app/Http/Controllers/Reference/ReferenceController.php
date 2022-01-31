<?php

namespace App\Http\Controllers\Reference;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reference\CreateReferenceRequest;
use App\Http\Requests\Reference\DeleteReferenceRequest;
use App\Http\Requests\Reference\FetchReferenceRequest;
use App\Http\Requests\Reference\UpdateReferenceRequest;
use App\Models\Reference;
use App\Services\Reference\ReferenceService;

class ReferenceController extends Controller
{

    // Local variable
    protected $referenceService;

    // Constructor
    public function __construct(ReferenceService $referenceService)
    {
        // Inject Service
        $this->referenceService = $referenceService;
    }
    // Create reference
    public function create(CreateReferenceRequest $request)
    {
        try {

            // Create reference service
            return $this->referenceService->createReference($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch user's all references
    public function fetch(FetchReferenceRequest $request)
    {
        try {

            // Fetch references
            return $this->referenceService->fetchReferences($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete user reference
    public function delete(DeleteReferenceRequest $request)
    {
        try {

            // Delete reference
            return $this->referenceService->deleteReference($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update Reference
    public function update(UpdateReferenceRequest $request)
    {
        try {

            // Update reference
            return $this->referenceService->updateReference($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}