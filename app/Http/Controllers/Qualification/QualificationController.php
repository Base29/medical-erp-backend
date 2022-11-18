<?php

namespace App\Http\Controllers\Qualification;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Qualification\CreateQualificationRequest;
use App\Http\Requests\Qualification\DeleteQualificationRequest;
use App\Http\Requests\Qualification\UpdateQualificationRequest;
use App\Services\Qualification\QualificationService;
use Exception;

class QualificationController extends Controller
{
    // Local variable
    protected $qualificationService;

    // Constructor
    public function __construct(QualificationService $qualificationService)
    {
        // Inject Service
        $this->qualificationService = $qualificationService;
    }

    // Create
    public function create(CreateQualificationRequest $request)
    {
        try {
            // Logic here
            return $this->qualificationService->createQualification($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update
    public function update(UpdateQualificationRequest $request)
    {
        try {
            // Logic here
            return $this->qualificationService->updateQualification($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete
    public function delete(DeleteQualificationRequest $request)
    {
        try {
            // Logic here
            return $this->qualificationService->deleteQualification($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}