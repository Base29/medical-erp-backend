<?php
namespace App\Services\HeadQuarter;

use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\HiringRequest;

class HeadQuarterService
{
    // Process hiring request
    public function processHiringRequest($request)
    {
        // Allowed fields
        $allowedFields = [
            'status',
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
        return $hiringRequest->with('workPatterns.workTimings', 'practice')
            ->latest('updated_at')
            ->first();
    }
}