<?php
namespace App\Services\JobSpecification;

use App\Helpers\Response;
use App\Models\JobResponsibility;
use App\Models\JobSpecification;
use App\Models\Practice;

class JobSpecificationService
{
    // Create job specification
    public function createJobSpecification($request)
    {
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

        // Save responsibilities
        $this->saveResponsibilities($request->responsibilities, $jobSpecification);

        // Return success response
        return $jobSpecification->with('practice', 'responsibilities')->latest()->first();
    }

    // Fetch job specifications
    public function fetchJobSpecifications($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get job specifications for $practice
        return JobSpecification::where('practice_id', $practice->id)
            ->with('practice', 'responsibilities')
            ->latest()
            ->get();
    }

    // Delete jpb specification
    public function deleteJobSpecification($request)
    {
        // Get Job specification
        $jobSpecification = JobSpecification::findOrFail($request->job_specification);

        // Delete Job Specification
        $jobSpecification->delete();
    }

    public function fetchSingleJobSpecification($request)
    {
        // Get job specification
        $jobSpecification = JobSpecification::where('id', $request->job_specification)
            ->with('practice', 'responsibilities')
            ->firstOrFail();

        // Return success response
        return Response::success([
            'job-specification' => $jobSpecification,
        ]);
    }

    // Save responsibilities
    private function saveResponsibilities($responsibilities, $jobSpecification)
    {
        foreach ($responsibilities as $responsibility) {
            // Instance of JobResponsibility
            $jobResponsibility = new JobResponsibility();
            $jobResponsibility->responsibility = $responsibility['responsibility'];

            // Save responsibility
            $jobSpecification->responsibilities()->save($jobResponsibility);
        }
    }
}