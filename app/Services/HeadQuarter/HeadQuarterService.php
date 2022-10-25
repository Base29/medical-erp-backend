<?php
namespace App\Services\HeadQuarter;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\HiringRequest;
use App\Models\Offer;
use App\Models\User;

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

        // Fetch users with the role recruiter
        $recruiters = User::whereHas('roles', function ($q) {
            $q->where('name', 'recruiter')->orWhere('name', 're');
        })
            ->get();

        // Get hiring request
        $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

        // Fetch $hiringRequest manager
        $manager = User::findOrFail($hiringRequest->notifiable);

        // Process hiring request
        $hiringRequestProcessed = UpdateService::updateModel($hiringRequest, $request->validated(), 'hiring_request');

        // Throw exception if processing failed
        if (!$hiringRequestProcessed) {
            throw new \Exception(ResponseMessage::customMessage('Something went wrong. Cannot process hiring request at the moment'));
        }

        // Looping through $recruiters
        foreach ($recruiters as $recruiter):
            // Send Notifications according to $request->gc_status
            switch ($request->status) {
                case 'approved':

                    break;

                case 'declined':
                    break;

                case 'escalated':
                    break;

                default:
                    return false;
            }
        endforeach;

        // Return success response
        return $hiringRequest->with('workPatterns.workTimings', 'practice')
            ->latest('updated_at')
            ->first();
    }

    // Fetch all offers
    public function fetchAllOffers()
    {
        // Get Offers
        $offers = Offer::with(['practice', 'hiringRequest', 'user.profile', 'workPattern.workTimings'])
            ->latest()
            ->paginate(10);

        // Getting count of permanent contract
        $made = $this->processCount('made');

        // Getting count of fixed term contract
        $accepted = $this->processCount('accepted');

        // Getting count of casual contract
        $pending = $this->processCount('pending');

        // Getting count of zero hour contract
        $declined = $this->processCount('declined');

        $countByStatus = collect(['count' => [
            'made' => $made,
            'accepted' => $accepted,
            'pending' => $pending,
            'declined' => $declined,
        ]]);

        $offersWithCount = $countByStatus->merge($offers);
        // Return success response
        return Response::success([
            'offers' => $offersWithCount,
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

    // Process count
    private function processCount($value)
    {
        return Offer::where('status', $value)->count();
    }
}