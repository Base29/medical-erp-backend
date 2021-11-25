<?php

namespace App\Http\Controllers\WorkPattern;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkPattern\CreateWorkPatternRequest;
use App\Models\WorkPattern;
use App\Models\WorkTiming;

class WorkPatternController extends Controller
{
    // Create work pattern
    public function create(CreateWorkPatternRequest $request)
    {
        try {

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

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch work patterns
    public function fetch()
    {
        // Fetch all work patterns
        $workPatterns = WorkPattern::latest()->get();

        return Response::success([
            'work_patterns' => $workPatterns,
        ]);
    }

    // Delete Work Pattern
    public function delete($id)
    {
        try {

            // Get work pattern
            $workPattern = WorkPattern::findOrFail($id);

            // Delete work pattern
            $workPattern->delete();

            return Response::success([
                'message' => ResponseMessage::deleteSuccess('Work Pattern'),
            ]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}