<?php
namespace App\Services\WorkTiming;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\WorkTiming;

class WorkTimingService
{
    // Update work timing
    public function updateWorkTiming($request)
    {
        // Allowed Fields
        $allowedFields = [
            'start_time',
            'end_time',
            'break_time',
            'repeat_days',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new \Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Get work timing
        $workTiming = WorkTiming::findOrFail($request->work_timing);

        // Update work timing
        $workTimingUpdated = UpdateService::updateModel($workTiming, $request->all(), 'work_timing');

        if (!$workTimingUpdated) {
            throw new \Exception(ResponseMessage::customMessage('Something went wrong while updating Work Timing ' . $workTiming->id));
        }

        return Response::success([
            'work_timing' => $workTiming->with('workPattern')->latest('updated_at')->first(),
        ]);
    }

    // Fetch work timing
    public function fetchWorkTimings($request)
    {
        $workTimings = WorkTiming::where('work_pattern_id', $request->work_pattern)->with('workPattern')->latest()->get();

        return Response::success([
            'work_timings' => $workTimings,
        ]);
    }
}