<?php

namespace App\Http\Controllers\PositionSummary;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\PositionSummary\CreatePositionSummaryRequest;
use App\Models\PositionSummary;

class PositionSummaryController extends Controller
{
    // Create position summary
    public function create(CreatePositionSummaryRequest $request)
    {
        try {

            // Creating position summary entry
            $positionSummary = new PositionSummary();
            $positionSummary->user_id = $request->id; //TODO: Switch this to be the id of ghost profile
            $positionSummary->job_title = $request->job_title;
            $positionSummary->contract_type = $request->contract_type;
            $positionSummary->department = $request->department;
            $positionSummary->reports_to = $request->reports_to;
            $positionSummary->probation_end_date = $request->probation_end_date;
            $positionSummary->notice_period = $request->notice_period;
            $positionSummary->save();
        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}