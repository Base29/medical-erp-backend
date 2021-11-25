<?php

namespace App\Http\Controllers\WorkTiming;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkTiming\FetchWorkTimingRequest;
use App\Http\Requests\WorkTiming\UpdateWorkTimingRequest;
use App\Models\WorkTiming;

class WorkTimingController extends Controller
{
    // Update work timing
    public function update(UpdateWorkTimingRequest $request)
    {
        try {
            // Allowed Fields
            $allowedFields = [
                'start_time',
                'end_time',
                'break_time',
                'repeat_days',
            ];

            // Checking if the $request doesn't contain any of the allowed fields
            if (!$request->hasAny($allowedFields)) {
                return Response::fail([
                    'message' => ResponseMessage::allowedFields($allowedFields),
                    'code' => 400,
                ]);
            }

            // Get work timing
            $workTiming = WorkTiming::findOrFail($request->work_timing);

            // Update work timing
            $workTimingUpdated = UpdateService::updateModel($workTiming, $request->all(), 'work_timing');

            if (!$workTimingUpdated) {
                return Response::fail([
                    'code' => 400,
                    'message' => ResponseMessage::customMessage('Something went wrong while updating Work Timing ' . $workTiming->id),
                ]);
            }

            return Response::success([
                'work_timing' => $workTiming->with('workPattern')->latest('updated_at')->first(),
            ]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch work timings for a work pattern
    public function fetch(FetchWorkTimingRequest $request)
    {
        try {

            $workTimings = WorkTiming::where('work_pattern_id', $request->work_pattern)->with('workPattern')->latest()->get();

            return Response::success([
                'work_timings' => $workTimings,
            ]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}