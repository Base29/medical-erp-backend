<?php

namespace App\Observers\Offer;

use App\Models\HiringRequest;
use App\Models\Offer;
use App\Models\User;
use App\Notifications\Offer\OfferAcceptedCandidateNotification;
use App\Notifications\Offer\OfferDeclinedCandidateNotification;
use Illuminate\Support\Facades\Config;

class OfferObserver
{
    /**
     * Handle the Offer "created" event.
     *
     * @param  \App\Models\Offer  $offer
     * @return void
     */
    public function created(Offer $offer)
    {
        //
    }

    /**
     * Handle the Offer "updated" event.
     *
     * @param  \App\Models\Offer  $offer
     * @return void
     */
    public function updated(Offer $offer)
    {
        // Get the candidate to be notified of the offer
        $notifiable = User::findOrFail($offer->user_id);

        // Get hiring request
        $hiringRequest = HiringRequest::findOrFail($offer->hiring_request_id);

        // Check status of the offer
        switch ($offer->status) {
            // Notify the candidate when the offer is accepted
            case Config::get('constants.OFFER.ACCEPTED'):
                $notifiable->notify(new OfferAcceptedCandidateNotification(
                    $offer,
                    $notifiable,
                    $hiringRequest
                ));
                break;
            case Config::get('constants.OFFER.DECLINED'):
                $notifiable->notify(new OfferDeclinedCandidateNotification(
                    $offer,
                    $notifiable,
                    $hiringRequest
                ));
                break;
        }

    }

    /**
     * Handle the Offer "deleted" event.
     *
     * @param  \App\Models\Offer  $offer
     * @return void
     */
    public function deleted(Offer $offer)
    {
        //
    }

    /**
     * Handle the Offer "restored" event.
     *
     * @param  \App\Models\Offer  $offer
     * @return void
     */
    public function restored(Offer $offer)
    {
        //
    }

    /**
     * Handle the Offer "force deleted" event.
     *
     * @param  \App\Models\Offer  $offer
     * @return void
     */
    public function forceDeleted(Offer $offer)
    {
        //
    }
}