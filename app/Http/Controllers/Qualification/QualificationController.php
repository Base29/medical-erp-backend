<?php

namespace App\Http\Controllers\Qualification;

use App\Http\Controllers\Controller;
use App\Http\Requests\Qualification\CreateQualificationRequest;
use App\Services\Qualification\QualificationService;

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

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}