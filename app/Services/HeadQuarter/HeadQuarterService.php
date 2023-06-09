<?php
namespace App\Services\HeadQuarter;

use App\Helpers\Response;
use App\Models\HiringRequest;
use App\Models\Offer;
use Illuminate\Support\Facades\Config;

class HeadQuarterService
{
    // Process hiring request
    public function processHiringRequest($request)
    {
        // Get hiring request
        $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

        // Update $hiringRequest
        $hiringRequest->status = $request->status;
        $hiringRequest->decision_reason = $request->decision_reason;
        $hiringRequest->decision_comment = $request->decision_comment;
        $hiringRequest->progress = $this->setProgress($request->status);

        // Save changes
        $hiringRequest->update();

        // Return success response
        return $hiringRequest->with('workPatterns.workTimings', 'practice')
            ->latest('updated_at')
            ->first();
    }

    // Fetch all offers
    public function fetchAllOffers()
    {
        // Get Offers
        $offers = Offer::where('is_active', Config::get('constants.OFFER.ACTIVE'))
            ->with(['practice', 'hiringRequest', 'user.profile', 'workPattern.workTimings'])
            ->latest()
            ->paginate(10);

        // Getting count of made offers
        $made = $this->processCount(2);

        // Getting count of accepted
        $accepted = $this->processCount(1);

        // Getting count of pending
        $pending = $this->processCount(4);

        // Getting count of declined
        $declined = $this->processCount(0);

        // Getting count of revised offers
        $revised = $this->processCount(3);

        $countByStatus = collect(['count' => [
            'made' => $made,
            'accepted' => $accepted,
            'pending' => $pending,
            'declined' => $declined,
            'revised' => $revised,
        ]]);

        $offersWithCount = $countByStatus->merge($offers);
        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
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
            'code' => Response::HTTP_OK,
            'search-results' => $results,
        ]);
    }

    // Process count
    private function processCount($value)
    {
        return Offer::where('status', $value)->count();
    }

    // Set progress for hiring request depending upon status
    private function setProgress($status)
    {
        // Initiating empty $progress variable
        $progress = null;

        // Set $progress depending upon the $status in a switch statement
        /**
         * Default = pending-approval
         *
         * Approved = in-process
         *
         * Hired = completed <-- This status will be updated via recruiter when a candidate is hired against the $hiringRequest
         *
         * Declined = declined
         *
         * Escalated = escalated
         */

        switch ($status) {
            case 'approved':
                $progress = 'in-process';
                break;

            case 'declined':
                $progress = 'declined';
                break;

            case 'escalated':
                $progress = 'in-process';
                break;
        }

        return $progress;
    }
}