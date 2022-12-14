<?php

namespace App\Observers\TrainingCourse;

use App\Models\TrainingCourse;

class TrainingCourseObserver
{
    /**
     * Handle the TrainingCourse "created" event.
     *
     * @param  \App\Models\TrainingCourse  $trainingCourse
     * @return void
     */
    public function created(TrainingCourse $trainingCourse)
    {
        // dd($trainingCourse->roles);
    }

    /**
     * Handle the TrainingCourse "updated" event.
     *
     * @param  \App\Models\TrainingCourse  $trainingCourse
     * @return void
     */
    public function updated(TrainingCourse $trainingCourse)
    {
        //
    }

    /**
     * Handle the TrainingCourse "deleted" event.
     *
     * @param  \App\Models\TrainingCourse  $trainingCourse
     * @return void
     */
    public function deleted(TrainingCourse $trainingCourse)
    {
        //
    }

    /**
     * Handle the TrainingCourse "restored" event.
     *
     * @param  \App\Models\TrainingCourse  $trainingCourse
     * @return void
     */
    public function restored(TrainingCourse $trainingCourse)
    {
        //
    }

    /**
     * Handle the TrainingCourse "force deleted" event.
     *
     * @param  \App\Models\TrainingCourse  $trainingCourse
     * @return void
     */
    public function forceDeleted(TrainingCourse $trainingCourse)
    {
        //
    }
}