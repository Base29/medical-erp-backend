<?php
namespace App\Services\Interview;

use App\Helpers\Response;
use App\Models\Interview;
use App\Models\Practice;

class InterviewService
{
    // Fetch all of practice's interviews
    public function fetchPracticeInterviews($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get $practice interviews
        $interviews = Interview::where('practice_id', $practice->id)
            ->with('practice')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'interviews' => $interviews,
        ]);
    }
}