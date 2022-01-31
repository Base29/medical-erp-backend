<?php

namespace App\Http\Controllers\JobSpecification;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\JobSpecification\CreateJobSpecificationRequest;
use App\Http\Requests\JobSpecification\DeleteJobSpecificationRequest;
use App\Http\Requests\JobSpecification\FetchJobSpecificationRequest;
use App\Models\JobSpecification;
use App\Services\JobSpecification\JobSpecificationService;

class JobSpecificationController extends Controller
{

    // Local variable
    protected $jobSpecificationService;

    // Constructor
    public function __construct(JobSpecificationService $jobSpecificationService)
    {
        // Inject service
        $this->jobSpecificationService = $jobSpecificationService;
    }

    // Create job specification
    public function create(CreateJobSpecificationRequest $request)
    {
        try {

            // Create job specification
            $jobSpecification = $this->jobSpecificationService->createJobSpecification($request);

            // Return success response
            return Response::success([
                'job-specification' => $jobSpecification,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch practice job specifications
    public function fetch(FetchJobSpecificationRequest $request)
    {
        try {

            // Fetch job specification
            $jobSpecifications = $this->jobSpecificationService->fetchJobSpecifications($request);

            // Return success response
            return Response::success([
                'job-specifications' => $jobSpecifications,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete job specification
    public function delete(DeleteJobSpecificationRequest $request)
    {
        try {

            // Delete job specification
            $this->jobSpecificationService->deleteJobSpecification($request);

            // Return success response
            return Response::success([
                'message' => ResponseMessage::deleteSuccess('Job Specification'),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}