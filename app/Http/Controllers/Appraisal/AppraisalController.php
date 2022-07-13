<?php

namespace App\Http\Controllers\Appraisal;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appraisal\CreateAppraisalRequest;
use App\Services\Appraisal\AppraisalService;

class AppraisalController extends Controller
{
    // Local variable
    protected $appraisalService;

    // Constructor
    public function __construct(AppraisalService $appraisalService)
    {
        // Inject Service
        $this->appraisalService = $appraisalService;
    }

    // Create
    public function create(CreateAppraisalRequest $request)
    {
        try {
            // Logic here
            return $this->appraisalService->createAppraisal($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}