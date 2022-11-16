<?php
namespace App\Services\Offer;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\HiringRequest;
use App\Models\Offer;
use App\Models\OfferAmendment;
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

        // Check if user already has a offer
        if ($hiringRequest->alreadyHasOffer($user->id)) {
            throw new \Exception(ResponseMessage::customMessage('User ' . $user->id . ' already has a offer for vacancy ' . $hiringRequest->id));
        }
        // Instance of Offer
        $offer = new Offer();
        $offer->practice_id = $practice->id;
        $offer->user_id = $user->id;
        $offer->work_pattern_id = $workPattern->id;
        $offer->status = 2;
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

    // Update offer
    public function updateOffer($request)
    {
        // Allowed fields
        $allowedFields = [
            'status',
            'amount',
            'work_pattern_id',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new \Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Get offer
        $offer = Offer::findOrFail($request->offer);

        // // Get work pattern
        // $workPattern = WorkPattern::findOrFail($request->work_pattern_id);

        // Update offer
        $offerUpdated = UpdateService::updateModel($offer, $request->validated(), 'offer');

        if (!$offerUpdated) {
            throw new \Exception(ResponseMessage::customMessage('Something went wrong while updating offer ' . $offer->id));
        }

        // Return success response
        return Response::success([
            'offer' => $offer->with('practice', 'hiringRequest', 'user.profile', 'workPattern.workTimings')
                ->latest('updated_at')
                ->first(),
        ]);

    }

    // Delete offer
    public function deleteOffer($request)
    {
        // Ger offer
        $offer = Offer::findOrFail($request->offer);

        // Delete offer
        $offer->delete();

        // Return success response
        return Response::success([
            'message' => ResponseMessage::deleteSuccess('Offer ' . $offer->id),
        ]);
    }

    public function fetchSingleOffer($request)
    {
        // Get offer
        $offer = Offer::where('id', $request->offer)
            ->with('practice', 'hiringRequest', 'user.profile', 'workPattern.workTimings')
            ->firstOrFail();

        // Return success response
        return Response::success([
            'offer' => $offer,
        ]);

    }

    // Amend offer
    public function amendHiringRequestOffer($request)
    {
        // Get Original offer
        $offer = Offer::findOrFail($request->offer);

        // Get all amendments of $offer
        $offerAmendments = $offer->amendments->toArray();

        // Get latest amendment from $offerAmendments
        $latestAmendment = end($offerAmendments);

        // Check if previous amendment has been rejected/declined
        if ($latestAmendment['status'] !== 0) {
            throw new \Exception(ResponseMessage::customMessage('Please Reject/Decline the previous amendment in order to create a new one.'));
        }

        // Create amendment for $offer
        $offerAmendment = new OfferAmendment();
        $offerAmendment->offer = $offer->id;
        $offerAmendment->work_pattern = $offer->work_pattern_id;
        $offerAmendment->amount = $request->amount;
        $offerAmendment->status = 2;
        $offerAmendment->save();

        // Change te status of the original offer to "revised"
        $offer->status = 3;
        $offer->save();

        // Return success response
        return Response::success([
            'offer' => $offer->where('id', $offer->id)->with('amendments')->first(),
        ]);

    }
}