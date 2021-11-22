<?php

namespace App\Http\Controllers\PositionSummary;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\PositionSummary\CreatePositionSummaryRequest;
use App\Http\Requests\PositionSummary\UpdatePositionSummaryRequest;
use App\Models\PositionSummary;
use App\Models\User;

class PositionSummaryController extends Controller
{
    // Create position summary
    public function create(CreatePositionSummaryRequest $request)
    {
        try {

            // Fetch user
            $user = User::findOrFail($request->user);

            // Creating position summary entry
            $positionSummary = new PositionSummary();
            $positionSummary->job_title = $request->job_title;
            $positionSummary->contract_type = $request->contract_type;
            $positionSummary->department = $request->department;
            $positionSummary->reports_to = $request->reports_to;
            $positionSummary->probation_end_date = $request->probation_end_date;
            $positionSummary->notice_period = $request->notice_period;
            $user->positionSummary()->save($positionSummary);

            // Return created Position Summary
            return Response::success([
                'position_summary' => $positionSummary->with('user')->latest()->first(),
            ]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update position summary
    public function update(UpdatePositionSummaryRequest $request)
    {
        try {

            // Allowed fields
            $allowedFields = [
                'job_title',
                'contract_type',
                'department',
                'reports_to',
                'probation_end_date',
                'notice_period',
            ];

            // Checking if the $request doesn't contain any of the allowed fields
            if (!$request->hasAny($allowedFields)) {
                return Response::fail([
                    'message' => ResponseMessage::allowedFields($allowedFields),
                    'code' => 400,
                ]);
            }

            // Fetch position summary
            $positionSummary = PositionSummary::findOrFail($request->position_summary);

            // Update position summary
            $positionSummaryUpdated = UpdateService::updateModel($positionSummary, $request->all(), 'position_summary');

            if ($positionSummaryUpdated) {
                return Response::success([
                    'position_summary' => $positionSummary->with('user')->latest('updated_at')->first(),
                ]);
            }

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}