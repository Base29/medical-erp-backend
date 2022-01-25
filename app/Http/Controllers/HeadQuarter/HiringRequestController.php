<?php

namespace App\Http\Controllers\HeadQuarter;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\HeadQuarter\ProcessHiringRequest;
use App\Models\HiringRequest;

class HiringRequestController extends Controller
{
    // Process hiring request
    public function processHiringRequest(ProcessHiringRequest $request)
    {
        try {
            // Allowed fields
            $allowedFields = [
                'is_approved',
                'is_declined',
                'is_escalated',
                'decision_reason',
                'decision_comment',
            ];

            // Checking if the $request doesn't contain any of the allowed fields
            if (!$request->hasAny($allowedFields)) {
                throw new \Exception(ResponseMessage::allowedFields($allowedFields));
            }

            // Get hiring request
            $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

            // Process hiring request
            $hiringRequestProcessed = UpdateService::updateModel($hiringRequest, $request->all(), 'hiring_request');

            // Throw exception if processing failed
            if (!$hiringRequestProcessed) {
                throw new \Exception(ResponseMessage::customMessage('Something went wrong. Cannot process hiring request at the moment'));
            }

            // Return success response
            return Response::success([
                'hiring-request' => $hiringRequest->with('workPatterns.workTimings', 'practice')
                    ->latest('updated_at')
                    ->first(),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}