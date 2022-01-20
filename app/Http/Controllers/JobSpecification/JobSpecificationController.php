<?php

namespace App\Http\Controllers\JobSpecification;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\JobSpecification\CreateJobSpecificationRequest;
use App\Http\Requests\JobSpecification\DeleteJobSpecificationRequest;
use App\Http\Requests\JobSpecification\FetchJobSpecificationRequest;
use App\Models\JobSpecification;
use App\Models\Practice;

class JobSpecificationController extends Controller
{
    // Create job specification
    public function create(CreateJobSpecificationRequest $request)
    {
        try {
            // Get practice
            $practice = Practice::findOrFail($request->practice);

            // Instance of JobSpecification model
            $jobSpecification = new JobSpecification();
            $jobSpecification->title = $request->title;
            $jobSpecification->salary_grade = $request->salary_grade;
            $jobSpecification->location = $request->location;
            $jobSpecification->total_hours = $request->total_hours;
            $jobSpecification->job_purpose = $request->job_purpose;

            // Save job specification
            $practice->jobSpecifications()->save($jobSpecification);

            // Return success response
            return Response::success([
                'job-specification' => $jobSpecification->with('practice')->latest()->first(),
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
            // Get practice
            $practice = Practice::findOrFail($request->practice);

            // Get job specifications for $practice
            $jobSpecifications = JobSpecification::where('practice_id', $practice->id)
                ->latest()
                ->get();

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
            // Get Job specification
            $jobSpecification = JobSpecification::findOrFail($request->job_specification);

            // Delete Job Specification
            $jobSpecification->delete();

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