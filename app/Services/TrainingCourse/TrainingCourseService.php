<?php

namespace App\Services\TrainingCourse;

use App\Helpers\Response;
use App\Models\TrainingCourse;

class TrainingCourseService
{
    public function createTrainingCourse($request)
    {
        // Initiate instance of TrainingCourse model
        $trainingCourse = new TrainingCourse();
        $trainingCourse->name = $request->name;
        $trainingCourse->type = $request->type;
        $trainingCourse->frequency = $request->frequency;
        $trainingCourse->save();

        /**
         * Attach course to a role
         */

        // Cast $request->roles to variable
        $roles = $request->roles;

        // Loop through $roles array
        foreach ($roles as $role):
            $trainingCourse->roles->attach($role['role']);
        endforeach;

        // Return success response
        return Response::success([
            'training-course' => $trainingCourse->with('roles')->first(),
        ]);
    }
}