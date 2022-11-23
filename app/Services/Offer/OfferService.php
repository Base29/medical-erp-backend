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
use Exception;
use Illuminate\Support\Carbon;

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

        // Check if $user applicant_status = NULL
        if ($user->applicant_status === null) {
            throw new Exception(ResponseMessage::customMessage('Cannot create offer for applicant. Status of the applicant is not updated'), Response::HTTP_CONFLICT);
        }

        // Check if $user applicant_status = 0
        if ($user->applicant_status === 0) {
            throw new Exception(ResponseMessage::customMessage('Cannot create offer for applicant. Applicant has been rejected in interview process.'), Response::HTTP_CONFLICT);
        }

        // Check if $user applicant_status = 2
        if ($user->applicant_status === 2) {
            throw new Exception(ResponseMessage::customMessage('Cannot create offer for applicant. Applicant has been referred for a second interview.'), Response::HTTP_CONFLICT);
        }

        //TODO: START-BLOCK - Logic in this code block for latest offer can be improved with the newly added is_active key in offers table

        // Get work pattern
        $workPattern = WorkPattern::findOrFail($request->work_pattern);

        // Cast $user->offers array to variable
        $userOffers = $user->offers->toArray();

        // Get applicant's latest offer
        $userLatestOffer = end($userOffers);

        // Id of the current offer
        $currentOfferId = null;

        // Check if $user has offers
        if ($userLatestOffer !== false) {

            // Cast id of the offer to $currentOfferId
            $currentOfferId = $userLatestOffer['id'];

            // Check if $userLatestOffer is discarded
            if ($userLatestOffer['status'] !== 5) {
                throw new Exception('Applicant already have a active offer.', Response::HTTP_CONFLICT);
            }
        }

        //TODO: END-BLOCK

        // Check if $request has joining_date
        if ($request->has('joining_date')) {
            // Check if joining_date is in the past.
            if (Carbon::createFromFormat('Y-m-d', $request->joining_date)->isPast()) {
                throw new Exception('Joining date is in the past.', Response::HTTP_BAD_REQUEST);
            }
        }

        // Instance of Offer
        $offer = new Offer();
        $offer->practice_id = $practice->id;
        $offer->user_id = $user->id;
        $offer->work_pattern_id = $workPattern->id;
        $offer->status = 2;
        $offer->is_active = 1;
        $offer->amount = $request->amount;
        $offer->joining_date = $request->joining_date;

        // Save offer
        $hiringRequest->offers()->save($offer);

        // Change the is_active status of the user's old offer
        if (isset($currentOfferId)) {
            // Get the old offer
            $oldOffer = Offer::findOrFail($currentOfferId);

            // Change is_active
            $oldOffer->is_active = 0;
            $oldOffer->save();

        }

        // Return success response
        return Response::success([
            'code' => Response::HTTP_CREATED,
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
            'reason',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new Exception(ResponseMessage::allowedFields($allowedFields), Response::HTTP_BAD_REQUEST);
        }

        // Get offer
        $offer = Offer::findOrFail($request->offer);

        // // Get work pattern
        // $workPattern = WorkPattern::findOrFail($request->work_pattern_id);

        // Update offer
        $offerUpdated = UpdateService::updateModel($offer, $request->validated(), 'offer');

        if (!$offerUpdated) {
            throw new Exception(ResponseMessage::customMessage('Something went wrong while updating offer ' . $offer->id), Response::HTTP_BAD_REQUEST);
        }

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
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
            'code' => Response::HTTP_OK,
            'offer' => $offer,
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
            'code' => Response::HTTP_OK,
            'offer' => $offer,
        ]);

    }

    // Amend offer
    public function amendHiringRequestOffer($request)
    {
        // Get Original offer
        $offer = Offer::findOrFail($request->offer);

        // Check if $offer is discarded (status = 5)
        if ($offer->status === 5) {
            throw new Exception(ResponseMessage::customMessage('Cannot create amendment for Offer. The offer is discarded (status = 5).'), Response::HTTP_FORBIDDEN);
        }

        // Get all amendments of $offer
        $offerAmendments = $offer->amendments->toArray();

        if (!empty($offerAmendments)) {
            // Get latest amendment from $offerAmendments
            $latestAmendment = end($offerAmendments);

            if ($latestAmendment !== false) {
                // Check if the previous amendment is accepted
                if ($latestAmendment['status'] === 1) {
                    throw new Exception(ResponseMessage::customMessage('The status of the previous amendment is "Accepted". No more amendments can be created for this offer'), Response::HTTP_FORBIDDEN);
                }

                // Check if previous amendment has been rejected/declined
                if ($latestAmendment['status'] !== 0) {
                    throw new Exception(ResponseMessage::customMessage('The status of previous amendment is "Negotiating". Please Reject/Decline the previous amendment in order to create a new one.'), Response::HTTP_FORBIDDEN);
                }
            }
        }

        // Check if $request has joining_date
        if ($request->has('joining_date')) {
            // Check if the joining_date is the past.
            if (Carbon::createFromFormat('Y-m-d', $request->joining_date)->isPast()) {
                throw new Exception(ResponseMessage::customMessage('Joining date is in the past.'), Response::HTTP_BAD_REQUEST);
            }
        }

        // Create amendment for $offer
        $offerAmendment = new OfferAmendment();
        $offerAmendment->offer = $offer->id;
        $offerAmendment->work_pattern = $offer->work_pattern_id;
        $offerAmendment->amount = $request->amount;
        $offerAmendment->status = 2;
        $offerAmendment->is_active = 1;
        $offerAmendment->joining_date = $request->has('joining_date') ? $request->joining_date : $offer->joining_date;
        $offerAmendment->save();

        // Change te status of the original offer to "revised"
        $offer->status = 3;
        $offer->save();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_CREATED,
            'offer' => $offer->where('id', $offer->id)->with('amendments')->first(),
        ]);

    }

    // Update offer amendment
    public function updateOfferAmendment($request)
    {
        // Get Offer amendment
        $offerAmendment = OfferAmendment::findOrFail($request->amendment);

        // Update status of the offer amendment
        $offerAmendment->status = $request->status;
        $offerAmendment->is_active = $request->status === 0 ? 0 : 1;
        $offerAmendment->reason = $request->reason ? $request->reason : null;
        $offerAmendment->save();

        // Check if $offerAmendment has been accepted
        if ($offerAmendment->status === 1) {

            // Update status of offer to accepted if the $offerAmendment if accepted
            $offer = Offer::findOrFail($offerAmendment->offer);
            $offer->status = 1;
            $offer->save();

        }

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'offer-amendment' => $offerAmendment->latest('updated_at')->first(),
        ]);
    }
}