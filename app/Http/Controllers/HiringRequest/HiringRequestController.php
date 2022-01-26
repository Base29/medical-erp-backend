<?php

namespace App\Http\Controllers\HiringRequest;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\HiringRequest\CreateHiringRequest;
use App\Http\Requests\HiringRequest\DeleteHiringRequest;
use App\Http\Requests\HiringRequest\FetchHiringRequest;
use App\Http\Requests\HiringRequest\FetchSingleHiringRequest;
use App\Http\Requests\HiringRequest\UpdateHiringRequest;
use App\Models\HiringRequest;
use App\Models\Practice;
use App\Models\WorkPattern;
use App\Models\WorkTiming;
use App\Services\HiringRequestService\HiringRequestService;

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

    // Fetch all hiring request for practice
    public function fetch(FetchHiringRequest $request)
    {
        try {
            // Get practice
            $practice = Practice::findOrFail($request->practice);

            // Get hiring requests
            $hiringRequests = HiringRequest::where('practice_id', $practice->id)
                ->with('practice', 'workPatterns.workTimings')
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

            // Return success response
            return Response::success([
                'hiring-requests' => $results,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Process count
    private function processCount($practiceId, $column, $value)
    {
        return HiringRequest::where(['practice_id' => $practiceId, $column => $value])->count();
    }
}