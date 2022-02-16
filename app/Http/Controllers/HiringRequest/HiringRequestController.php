<?php

namespace App\Http\Controllers\HiringRequest;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\HiringRequest\AddApplicantToHiringRequest;
use App\Http\Requests\HiringRequest\CreateHiringRequest;
use App\Http\Requests\HiringRequest\DeleteHiringRequest;
use App\Http\Requests\HiringRequest\FetchHiringRequest;
use App\Http\Requests\HiringRequest\FetchSingleHiringRequest;
use App\Http\Requests\HiringRequest\UpdateHiringRequest;
use App\Models\HiringRequest;
use App\Services\HiringRequest\HiringRequestService;

class HiringRequestController extends Controller
{
    protected $hiringRequestService;

    public function __construct(HiringRequestService $hiringRequestService)
    {
        $this->hiringRequestService = $hiringRequestService;
    }

    // Create hiring request
    public function create(CreateHiringRequest $request)
    {
        try {

            // New hiring request
            $hiringRequest = $this->hiringRequestService->createHiringRequest($request);

            // Return success response
            return Response::success([
                'hiring-request' => $hiringRequest,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch hiring request
    public function fetchSingle(FetchSingleHiringRequest $request)
    {
        try {

            // Fetch single Hiring request
            $hiringRequest = $this->hiringRequestService->fetchSingleHiringRequest($request);

            // Return success response
            return Response::success([
                'hiring-request' => $hiringRequest,
            ]);
        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update hiring request
    public function update(UpdateHiringRequest $request)
    {
        try {
            // Update hiring request
            $hiringRequest = $this->hiringRequestService->updateHiringRequest($request);

            // Return success response
            return Response::success([
                'hiring-request' => $hiringRequest,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete hiring request
    public function delete(DeleteHiringRequest $request)
    {
        try {

            // Delete hiring request
            $this->hiringRequestService->deleteHiringRequest($request);

            // Return success response
            return Response::success([
                'message' => ResponseMessage::deleteSuccess('Hiring Request'),
            ]);
        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch all hiring request for practice
    public function fetch(FetchHiringRequest $request)
    {
        try {

            // Fetch hiring requests
            $hiringRequests = $this->hiringRequestService->fetchHiringRequests($request);

            // Return success response
            return Response::success([
                'hiring-requests' => $hiringRequests,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Add applicant
    public function addApplicant(AddApplicantToHiringRequest $request)
    {
        try {
            // Add applicant to hiring request
            return $this->hiringRequestService->addApplicantToHiringRequest($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}