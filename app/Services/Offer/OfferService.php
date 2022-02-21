<?php
namespace App\Services\Offer;

use App\Helpers\Response;
use App\Models\HiringRequest;
use App\Models\Offer;
use App\Models\Practice;
use App\Models\User;
use App\Models\WorkPattern;

class OfferService
{
    // Create offer
    public function createOffer($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get hiring request
        $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

        // Get user
        $user = User::findOrFail($request->user);

        // Get work pattern
        $workPattern = WorkPattern::findOrFail($request->work_pattern);

        // Instance of Offer
        $offer = new Offer();
        $offer->practice_id = $practice->id;
        $offer->user_id = $user->id;
        $offer->work_pattern_id = $workPattern->id;
        $offer->status = $request->status;
        $offer->amount = $request->amount;

        // Save offer
        $hiringRequest->offers()->save($offer);

        // Return success response
        return Response::success([
            'offer' => $offer->with('practice', 'hiringRequest', 'user.profile', 'workPattern.workTimings')
                ->latest()
                ->first(),
        ]);
    }
}