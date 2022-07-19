<?php

namespace App\Http\Controllers\Appraisal;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appraisal\CreateAppraisalRequest;
use App\Http\Requests\Appraisal\FetchAllAppraisalsRequest;
use App\Http\Requests\Appraisal\FetchUpcomingAppraisalsRequest;
use App\Http\Requests\Appraisal\UpdateAppraisalRequest;
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

    // Fetch interviews of practice
    public function fetch(FetchAllAppraisalsRequest $request)
    {
        try {
            // Fetch practice interview
            return $this->appraisalService->fetchAllAppraisals($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch practice interview schedules
    public function upcomingAppraisals(FetchUpcomingAppraisalsRequest $request)
    {
        try {
            // Fetch practice interview schedules
            return $this->appraisalService->fetchUpcomingAppraisals($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update
    public function update(UpdateAppraisalRequest $request)
    {
        try {
            // Update appraisal
            return $this->appraisalService->updateAppraisal($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}