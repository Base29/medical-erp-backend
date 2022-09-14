<?php
namespace App\Services\PositionSummary;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\PositionSummary;
use App\Models\User;

class PositionSummaryService
{
    // Create position summary
    public function createPositionSummary($request)
    {
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
            'position-summary' => $positionSummary->with('user.profile')->latest()->first(),
        ]);
    }

    // Update position summary
    public function updatePositionSummary($request)
    {
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
            throw new \Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Fetch position summary
        $positionSummary = PositionSummary::findOrFail($request->position_summary);

        // Update position summary
        UpdateService::updateModel($positionSummary, $request->validated(), 'position_summary');

        return Response::success([
            'position_summary' => $positionSummary->with('user.profile')->latest('updated_at')->first(),
        ]);

    }

    // Fetch Single position summary
    public function fetchSinglePositionSummary($request)
    {
        // Fetch single contract summary
        $positionSummary = PositionSummary::where('id', $request->position_summary)->with('user.profile')->first();

        // Return response with the Contract Summary
        return Response::success([
            'position_summary' => $positionSummary,
        ]);
    }

    // Delete position summary
    public function deletePositionSummary($id)
    {
        // Fetch position summary
        $positionSummary = PositionSummary::findOrFail($id);

        if (!$positionSummary) {
            throw new \Exception(ResponseMessage::notFound('Position Summary', $id, false));
        }

        $positionSummary->delete();

        return Response::success([
            'message' => ResponseMessage::deleteSuccess('Position Summary'),
        ]);
    }
}
