<?php

namespace App\Http\Controllers\HiringRequest;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\HiringRequest\CreateHiringRequest;
use App\Http\Requests\HiringRequest\DeleteHiringRequest;
use App\Http\Requests\HiringRequest\FetchSingleHiringRequest;
use App\Http\Requests\HiringRequest\UpdateHiringRequest;
use App\Models\HiringRequest;
use App\Models\Practice;
use App\Models\WorkPattern;
use App\Models\WorkTiming;

class HiringRequestController extends Controller
{
    // Create hiring request
    public function create(CreateHiringRequest $request)
    {
        try {
            // Get practice
            $practice = Practice::findOrFail($request->practice);

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
                    return Response::fail([
                        'message' => ResponseMessage::customMessage('Selected Rota with id ' . $request->rota_information . ' is invalid. Supply following fields to create new rota ' . implode("|", $requiredFields)),
                        'code' => 400,
                    ]);
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
            $hiringRequest->job_specification = $request->job_specification;
            $hiringRequest->person_specification = $request->person_specification;

            // Save hiring request
            $practice->hiringRequests()->save($hiringRequest);

            // Attach work pattern with the hiring request
            $hiringRequest->workPatterns()->attach($workPatternId);

            // Return success response
            return Response::success([
                'hiring-request' => $hiringRequest->with('workPatterns')->latest()->first(),
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
            // Get hiring request
            $hiringRequest = HiringRequest::where('id', $request->hiring_request)->with('workPatterns')->get();

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
                'job_specification',
                'person_specification',
                'rota_information',
            ];

            // Checking if the $request doesn't contain any of the allowed fields
            if (!$request->hasAny($allowedFields)) {
                return Response::fail([
                    'message' => ResponseMessage::allowedFields($allowedFields),
                    'code' => 400,
                ]);
            }

            // Get hiring request
            $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

            // If updating only work pattern
            if ($request->has('rota_information')) {
                // Get work pattern
                $workPattern = WorkPattern::find($request->rota_information);

                // If work pattern doesn't exist with the provided $request->rota_information
                if (!$workPattern) {
                    return Response::fail([
                        'code' => 404,
                        'message' => ResponseMessage::customMessage('Work Pattern with the provided id ' . $request->rota_information . ' not found. Please provide correct work pattern id or create a new work pattern'),
                    ]);
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
                        return Response::fail([
                            'message' => ResponseMessage::customMessage('Selected Rota with id ' . $request->rota_information . ' is invalid. Supply following fields to create new rota ' . implode("|", $requiredFields)),
                            'code' => 400,
                        ]);
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
                return Response::success([
                    'hiring-request' => $hiringRequest->where('id', $request->rota_information)->with('workPatterns')->get(),
                ]);

            }

            // Update hiring request except work pattern
            $hiringRequestUpdated = UpdateService::updateModel($hiringRequest, $request->all(), 'hiring_request');

            // Return fail response
            if (!$hiringRequestUpdated) {
                return Response::fail([
                    'code' => 400,
                    'message' => ResponseMessage::customMessage('Something went wrong while updating Hiring Request'),
                ]);
            }

            // Return success response
            return Response::success([
                'hiring-request' => $hiringRequest->with('workPatterns')->latest('updated_at')->first(),
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
            // Get hiring request
            $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

            // Delete hiring request
            $hiringRequest->delete();

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
}