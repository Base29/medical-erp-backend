<?php

namespace App\Observers\HiringRequest;

use App\Models\HiringRequest;
use App\Models\User;
use App\Notifications\HiringRequest\ApproveHiringRequestNotification;
use App\Notifications\HiringRequest\EscalateHiringRequestNotification;
use App\Notifications\HiringRequest\NewHiringRequestNotification;
use App\Notifications\HiringRequest\NotifyHiringRequestManagerNotification;

class HiringRequestObserver
{
    /**
     * Handle the HiringRequest "created" event.
     *
     * @param  \App\Models\HiringRequest  $hiringRequest
     * @return void
     */
    public function created(HiringRequest $hiringRequest)
    {
        // Get authenticated user
        $authenticatedUser = auth()->user();

        // Get users with HQ role
        $hqUsers = User::whereHas('roles', function ($q) {
            $q->where('name', 'hq')->orWhere('name', 'headquarter');
        })->get();

        // Looping through $hqUsers and sending notification of new $hiringRequest
        foreach ($hqUsers as $hqUser):
            $hqUser->notify(new NewHiringRequestNotification(
                $hqUser,
                $hiringRequest,
                $authenticatedUser
            ));
        endforeach;
    }

    /**
     * Handle the HiringRequest "updated" event.
     *
     * @param  \App\Models\HiringRequest  $hiringRequest
     * @return void
     */
    public function updated(HiringRequest $hiringRequest)
    {

        // Check origin oof request
        if (request()->is('api/hq/hiring-requests/process-hiring-request')) {
            // Fetch $hiringRequest manager
            $manager = User::findOrFail($hiringRequest->notifiable);

            // HQ User
            $hqUser = auth()->user();

            // Fetch users with the role recruiter
            $recruiters = User::whereHas('roles', function ($q) {
                $q->where('name', 'recruiter')->orWhere('name', 're');
            })
                ->get();

            // Looping through $recruiters
            foreach ($recruiters as $recruiter):
                // Send Notifications according to $request->gc_status
                switch (request()->status) {
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
        }

    }

    /**
     * Handle the HiringRequest "deleted" event.
     *
     * @param  \App\Models\HiringRequest  $hiringRequest
     * @return void
     */
    public function deleted(HiringRequest $hiringRequest)
    {
        //
    }

    /**
     * Handle the HiringRequest "restored" event.
     *
     * @param  \App\Models\HiringRequest  $hiringRequest
     * @return void
     */
    public function restored(HiringRequest $hiringRequest)
    {
        //
    }

    /**
     * Handle the HiringRequest "force deleted" event.
     *
     * @param  \App\Models\HiringRequest  $hiringRequest
     * @return void
     */
    public function forceDeleted(HiringRequest $hiringRequest)
    {
        //
    }
}