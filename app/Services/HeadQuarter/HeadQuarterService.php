<?php
namespace App\Services\HeadQuarter;

use App\Helpers\Response;
use App\Models\HiringRequest;
use App\Models\Offer;
use App\Models\User;
use App\Notifications\HiringRequest\ApproveHiringRequestNotification;
use App\Notifications\HiringRequest\EscalateHiringRequestNotification;
use App\Notifications\HiringRequest\NotifyHiringRequestManagerNotification;

class HeadQuarterService
{
    // Process hiring request
    public function processHiringRequest($request)
    {
        // Fetch users with the role recruiter
        $recruiters = User::whereHas('roles', function ($q) {
            $q->where('name', 'recruiter')->orWhere('name', 're');
        })
            ->get();

        // Get hiring request
        $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

        // Fetch $hiringRequest manager
        $manager = User::findOrFail($hiringRequest->notifiable);

        // HQ User
        $hqUser = auth()->user();

        // Update $hiringRequest
        $hiringRequest->status = $request->status;
        $hiringRequest->decision_reason = $request->decision_reason;
        $hiringRequest->decision_comment = $request->decision_comment;
        $hiringRequest->progress = $this->setProgress($request->status);

        // Save changes
        $hiringRequest->save();

        // Looping through $recruiters
        foreach ($recruiters as $recruiter):
            // Send Notifications according to $request->gc_status
            switch ($request->status) {
                case 'approved':

                    // Notify $recruiter that the $hiringRequest has been approved
                    $recruiter->notify(new ApproveHiringRequestNotification(
                        $hqUser,
                        $hiringRequest,
                        $recruiter
                    ));

                    break;

                case 'escalated':
                    $recruiter->notify(new EscalateHiringRequestNotification(
                        $hqUser,
                        $hiringRequest,
                        $recruiter
                    ));
                    break;
            }
        endforeach;

        // Notify $manager regarding the action taken by HQ on the $hiringRequest
        $manager->notify(new NotifyHiringRequestManagerNotification(
            $manager,
            $hiringRequest,
            $hqUser
        ));

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
                $progress = 'escalated';
                break;
        }

        return $progress;
    }
}