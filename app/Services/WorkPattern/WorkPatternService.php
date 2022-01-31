<?php
namespace App\Services\WorkPattern;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Models\WorkPattern;
use App\Models\WorkTiming;

class WorkPatternService
{
    // Create work pattern
    public function createWorkPattern($request)
    {
        // Create work pattern
        $workPattern = new WorkPattern();
        $workPattern->name = $request->name;
        $workPattern->save();

        // Loop through the work_timings array in request
        foreach ($request->work_timings as $work_timing) {
            // Create Work Timing
            $workTiming = new WorkTiming();
            $workTiming->work_pattern_id = $workPattern->id;
            $workTiming->start_time = $work_timing['start_time'];
            $workTiming->end_time = $work_timing['end_time'];
            $workTiming->break_time = $work_timing['break_time'];
            $workTiming->repeat_days = $work_timing['repeat_days'];
            $workPattern->workTimings()->save($workTiming);
        }

        return Response::success([
            'work_pattern' => $workPattern->with('workTimings')->latest()->first(),
        ]);
    }

    // Fetch work patterns
    public function fetchWorkPatterns()
    {
        // Fetch all work patterns
        $workPatterns = WorkPattern::with('workTimings')->latest()->get();

        return Response::success([
            'work_patterns' => $workPatterns,
        ]);
    }

    // Delete work patterns
    public function deleteWorkPattern($id)
    {
        // Get work pattern
        $workPattern = WorkPattern::findOrFail($id);

        // Delete work pattern
        $workPattern->delete();

        return Response::success([
            'message' => ResponseMessage::deleteSuccess('Work Pattern'),
        ]);
    }
}