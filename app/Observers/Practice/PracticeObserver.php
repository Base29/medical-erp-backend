<?php

namespace App\Observers\Practice;

use App\Helpers\FileUploadService;
use App\Models\Practice;

class PracticeObserver
{
    /**
     * Handle the Practice "created" event.
     *
     * @param  \App\Models\Practice  $practice
     * @return void
     */
    public function created(Practice $practice)
    {
        // Check if request has logo
        if (request()->has('logo')) {
            $folderPath = 'practices/practice-' . $practice->id . '/logo';

            $logoUrl = FileUploadService::upload(request()->logo, $folderPath, 's3');

            // Save logo url to database
            $practice->logo = $logoUrl;
            $practice->save();
        }

    }

    /**
     * Handle the Practice "updated" event.
     *
     * @param  \App\Models\Practice  $practice
     * @return void
     */
    public function updated(Practice $practice)
    {
        //
    }

    /**
     * Handle the Practice "deleted" event.
     *
     * @param  \App\Models\Practice  $practice
     * @return void
     */
    public function deleted(Practice $practice)
    {
        //
    }

    /**
     * Handle the Practice "restored" event.
     *
     * @param  \App\Models\Practice  $practice
     * @return void
     */
    public function restored(Practice $practice)
    {
        //
    }

    /**
     * Handle the Practice "force deleted" event.
     *
     * @param  \App\Models\Practice  $practice
     * @return void
     */
    public function forceDeleted(Practice $practice)
    {
        //
    }
}