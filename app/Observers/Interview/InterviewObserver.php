<?php

namespace App\Observers\Interview;

use App\Models\HiringRequest;
use App\Models\InterviewSchedule;
use App\Models\User;
use App\Notifications\Interview\InviteAdditionalStaffNotification;
use App\Notifications\Interview\InviteHQStaffNotification;

class InterviewObserver
{
    /**
     * Handle the InterviewSchedule "created" event.
     *
     * @param  \App\Models\InterviewSchedule  $interviewSchedule
     * @return void
     */
    public function created(InterviewSchedule $interviewSchedule)
    {

        // Get hiring request
        $hiringRequest = HiringRequest::findOrFail($interviewSchedule->hiring_request_id);

        // Send notifications to additional staff if invited
        if (!empty($interviewSchedule->additional_staff) && !is_null($interviewSchedule->additional_staff)) {
            // Get info for additional staff
            $additionalStaff = User::findOrFail($interviewSchedule->additional_staff);

            // Notify
            $additionalStaff->notify(new InviteAdditionalStaffNotification(
                $additionalStaff,
                $interviewSchedule,
                $hiringRequest
            ));
        }

        // Send notification to HQ staff if invited
        if (!empty($interviewSchedule->hq_staff) && !is_null($interviewSchedule->hq_staff)) {
            // Get info for hq staff
            $hqStaff = User::findOrFall($interviewSchedule->hq_staff);

            // Notify
            $hqStaff->notify(new InviteHQStaffNotification(
                $hqStaff,
                $hiringRequest,
                $interviewSchedule
            ));

        }
    }

    /**
     * Handle the InterviewSchedule "updated" event.
     *
     * @param  \App\Models\InterviewSchedule  $interviewSchedule
     * @return void
     */
    public function updated(InterviewSchedule $interviewSchedule)
    {
        //
    }

    /**
     * Handle the InterviewSchedule "deleted" event.
     *
     * @param  \App\Models\InterviewSchedule  $interviewSchedule
     * @return void
     */
    public function deleted(InterviewSchedule $interviewSchedule)
    {
        //
    }

    /**
     * Handle the InterviewSchedule "restored" event.
     *
     * @param  \App\Models\InterviewSchedule  $interviewSchedule
     * @return void
     */
    public function restored(InterviewSchedule $interviewSchedule)
    {
        //
    }

    /**
     * Handle the InterviewSchedule "force deleted" event.
     *
     * @param  \App\Models\InterviewSchedule  $interviewSchedule
     * @return void
     */
    public function forceDeleted(InterviewSchedule $interviewSchedule)
    {
        //
    }
}