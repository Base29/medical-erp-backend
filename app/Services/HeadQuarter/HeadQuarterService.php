<?php
namespace App\Services\HeadQuarter;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\HiringRequest;
use App\Models\Offer;

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
            'progress',
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

    // Fetch all offers
    public function fetchAllOffers()
    {
        // Get Offers
        $offers = Offer::with('practice', 'hiringRequest', 'user', 'workPattern.workTimings')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'offers' => $offers,
        ]);
    }

    // Search hiring requests
    public function searchHiringRequest($request)
    {

        // Search results
        $results = HiringRequest::where($request->field, 'LIKE', '%' . $request->search_term . '%')
            ->with('applicationManager.profile', 'practice', 'workPatterns.workTimings', 'jobSpecification', 'personSpecification.personSpecificationAttributes', 'profiles', 'department', 'applicants.profile')
            ->latest()
            ->paginate(10);

        // Return search results
        return Response::success([
            'search-results' => $results,
        ]);
    }
}