<?php

/**
 *
 */

namespace App\Services\HiringRequest;

use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\HiringRequest;
use App\Models\JobSpecification;
use App\Models\PersonSpecification;
use App\Models\Practice;
use App\Models\WorkPattern;
use App\Models\WorkTiming;

class HiringRequestService
{
    // Hiring Request Create
    public function createHiringRequest($request)
    {

        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get job specification
        $jobSpecification = JobSpecification::findOrFail($request->job_specification);

        // Get person specification
        $personSpecification = PersonSpecification::findOrFail($request->person_specification);

        // Get work pattern
        $workPattern = WorkPattern::find($request->rota_information);

        // Cast id of $workPattern to a variable
        $workPatternId = $workPattern ? $workPattern->id : null;

        // If $workPattern is false
        if (!$workPattern) {

            // Fields required for creating a new work pattern
            $requiredFields = [
                'name',
                'start_time',
                'end_time',
                'break_time',
                'repeat_days',
            ];

            // Checking if the $request doesn't contain any of the allowed fields
            if (!$request->has($requiredFields)) {
                throw new \Exception(ResponseMessage::customMessage('Selected Rota with id ' . $request->rota_information . ' is invalid. Supply following fields to create new rota ' . implode("|", $requiredFields)));
            }

            // Create work pattern
            $workPattern = new WorkPattern();
            $workPattern->name = $request->name;
            $workPattern->save();

            // Create Work Timing
            $workTiming = new WorkTiming();
            $workTiming->work_pattern_id = $workPattern->id;
            $workTiming->start_time = $request->start_time;
            $workTiming->end_time = $request->end_time;
            $workTiming->break_time = $request->break_time;
            $workTiming->repeat_days = $request->repeat_days;
            $workPattern->workTimings()->save($workTiming);

            $workPatternId = $workPattern->id;

        }

        // Instance of HiringRequest
        $hiringRequest = new HiringRequest();
        $hiringRequest->job_title = $request->job_title;
        $hiringRequest->contract_type = $request->contract_type;
        $hiringRequest->department = $request->department;
        $hiringRequest->reporting_to = $request->reporting_to;
        $hiringRequest->start_date = $request->start_date;
        $hiringRequest->starting_salary = $request->starting_salary;
        $hiringRequest->reason_for_recruitment = $request->reason_for_recruitment;
        $hiringRequest->comment = $request->comment;
        $hiringRequest->job_specification_id = $jobSpecification->id;
        $hiringRequest->person_specification_id = $personSpecification->id;

        // Save hiring request
        $practice->hiringRequests()->save($hiringRequest);

        // Attach work pattern with the hiring request
        $hiringRequest->workPatterns()->attach($workPatternId);

        // Return newly created $hiringRequest
        return $hiringRequest->with('practice', 'workPatterns.workTimings', 'jobSpecification', 'personSpecification', 'profiles')
            ->latest()
            ->first();
    }

    // Fetch single hiring request
    public function fetchSingleHiringRequest($request)
    {
        // Get hiring request
        return HiringRequest::where('id', $request->hiring_request)
            ->with('practice', 'workPatterns.workTimings', 'jobSpecification', 'personSpecification', 'profiles')
            ->get();
    }

    // Update hiring request
    public function updateHiringRequest($request)
    {
        // Allowed fields
        $allowedFields = [
            'job_title',
            'contract_type',
            'department',
            'reporting_to',
            'start_date',
            'starting_salary',
            'reason_for_recruitment',
            'comment',
            'rota_information',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new \Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Get hiring request
        $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

        // If updating only work pattern
        if ($request->has('rota_information')) {
            // Get work pattern
            $workPattern = WorkPattern::find($request->rota_information);

            // If work pattern doesn't exist with the provided $request->rota_information
            if (!$workPattern) {
                throw new \Exception(ResponseMessage::customMessage('Work Pattern with the provided id ' . $request->rota_information . ' not found. Please provide correct work pattern id or create a new work pattern'));
            }

            // Cast id of $workPattern to a variable
            $workPatternId = $workPattern ? $workPattern->id : null;

            // If $workPattern is false
            if (!$workPattern) {

                // Fields required for creating a new work pattern
                $requiredFields = [
                    'name',
                    'start_time',
                    'end_time',
                    'break_time',
                    'repeat_days',
                ];

                // Checking if the $request doesn't contain any of the allowed fields
                if (!$request->has($requiredFields)) {
                    throw new \Exception(ResponseMessage::customMessage('Selected Rota with id ' . $request->rota_information . ' is invalid. Supply following fields to create new rota ' . implode("|", $requiredFields)));
                }

                // Create work pattern
                $workPattern = new WorkPattern();
                $workPattern->name = $request->name;
                $workPattern->save();

                // Create Work Timing
                $workTiming = new WorkTiming();
                $workTiming->work_pattern_id = $workPattern->id;
                $workTiming->start_time = $request->start_time;
                $workTiming->end_time = $request->end_time;
                $workTiming->break_time = $request->break_time;
                $workTiming->repeat_days = $request->repeat_days;
                $workPattern->workTimings()->save($workTiming);

                $workPatternId = $workPattern->id;

            }

            // Attach work pattern with the hiring request
            $hiringRequest->workPatterns()->attach($workPatternId);

            // Return success response
            return $hiringRequest->where('id', $request->rota_information)
                ->with('practice', 'workPatterns.workTimings', 'jobSpecification', 'personSpecification', 'profiles')
                ->get();

        }

        // Update hiring request except work pattern
        $hiringRequestUpdated = UpdateService::updateModel($hiringRequest, $request->all(), 'hiring_request');

        // Return fail response
        if (!$hiringRequestUpdated) {
            throw new \Exception(ResponseMessage::customMessage('Something went wrong while updating Hiring Request'));
        }

        // Return success response
        return $hiringRequest->with('practice', 'workPatterns.workTimings', 'jobSpecification', 'personSpecification', 'profiles')
            ->latest('updated_at')
            ->first();
    }

    // Delete hiring request
    public function deleteHiringRequest($request)
    {
        // Get hiring request
        $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

        // Delete hiring request
        $hiringRequest->delete();
    }

    // Fetch Hiring requests
    public function fetchHiringRequests($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get hiring requests
        $hiringRequests = HiringRequest::where(['practice_id' => $practice->id, 'status' => $request->status])
            ->with('practice', 'workPatterns.workTimings', 'jobSpecification', 'personSpecification', 'profiles')
            ->latest()
            ->paginate(10);

        // Casting $hiringRequests to $results and converting the object to array
        $results = $hiringRequests->toArray();

        // Getting count of approved hiring requests
        $approved = $this->processCount($practice->id, 'status', 'approved');

        // Getting count of declined hiring requests
        $declined = $this->processCount($practice->id, 'status', 'declined');

        // Getting count of escalated hiring requests
        $escalated = $this->processCount($practice->id, 'status', 'escalated');

        // Adding extra meta to response $results
        $results['approvedCount'] = $approved;
        $results['declinedCount'] = $declined;
        $results['escalatedCount'] = $escalated;

        // Return hiring requests
        return $results;
    }

    // Process count
    private function processCount($practiceId, $column, $value)
    {
        return HiringRequest::where(['practice_id' => $practiceId, $column => $value])->count();
    }
}