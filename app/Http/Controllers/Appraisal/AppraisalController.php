<?php

namespace App\Http\Controllers\Appraisal;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appraisal\ConcludeAppraisalRequest;
use App\Http\Requests\Appraisal\CreateAppraisalAnswerRequest;
use App\Http\Requests\Appraisal\CreateAppraisalRequest;
use App\Http\Requests\Appraisal\DeleteAppraisalRequest;
use App\Http\Requests\Appraisal\FetchAllAppraisalsRequest;
use App\Http\Requests\Appraisal\FetchCompletedAppraisalsRequest;
use App\Http\Requests\Appraisal\FetchOverdueAppraisalsRequest;
use App\Http\Requests\Appraisal\FetchSingleAppraisalRequest;
use App\Http\Requests\Appraisal\FetchUpcomingAppraisalsRequest;
use App\Http\Requests\Appraisal\UpdateAppraisalRequest;
use App\Services\Appraisal\AppraisalService;
use Exception;

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

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
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

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
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

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
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

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete
    public function delete(DeleteAppraisalRequest $request)
    {
        try {
            // Delete appraisal
            return $this->appraisalService->deleteAppraisal($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Completed Appraisals
    public function completedAppraisals(FetchCompletedAppraisalsRequest $request)
    {
        try {
            // Past appraisal service
            return $this->appraisalService->fetchCompletedAppraisals($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Store appraisal answers
    public function appraisalAnswer(CreateAppraisalAnswerRequest $request)
    {
        try {
            // Store appraisal answer
            return $this->appraisalService->storeAppraisalAnswer($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single appraisal
    public function singleAppraisal(FetchSingleAppraisalRequest $request)
    {
        try {
            // Fetch single interview service
            return $this->appraisalService->fetchSingleAppraisal($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Conclude appraisal
    public function concludeAppraisal(ConcludeAppraisalRequest $request)
    {
        try {
            // Logic here
            return $this->appraisalService->appraisalConclusion($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch overdue appraisals
    public function overdueAppraisals(FetchOverdueAppraisalsRequest $request)
    {
        try {
            // Logic here
            return $this->appraisalService->fetchOverdueAppraisals($request);
        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}