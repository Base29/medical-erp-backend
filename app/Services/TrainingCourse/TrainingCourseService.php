<?php

namespace App\Services\TrainingCourse;

use App\Helpers\Response;
use App\Models\CourseModule;
use App\Models\ModuleLesson;
use App\Models\TrainingCourse;

class TrainingCourseService
{
    // Create training course
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
            $trainingCourse->roles()->attach($role['role']);
        endforeach;

        // Return success response
        return Response::success([
            'training-course' => $trainingCourse->with('roles')->latest()->first(),
        ]);
    }

    // Create module
    public function createCourseModule($request)
    {
        // Get training course
        $trainingCourse = TrainingCourse::findOrFail($request->course);

        // Initiate instance of CourseModule
        $courseModule = new CourseModule();
        $courseModule->course = $trainingCourse->id;
        $courseModule->name = $request->name;
        $courseModule->duration = $request->duration;
        $courseModule->is_required = $request->is_required;
        $courseModule->frequency = $request->frequency;
        $courseModule->reminder = $request->reminder;

        // Save Module
        $courseModule->save();

        // Return success response
        return Response::success([
            'course-module' => $courseModule,
        ]);
    }

    // Create lesson
    public function createModuleLesson($request)
    {
        // Get Course Module
        $courseModule = CourseModule::findOrFail($request->module);

        // Initiate instance of ModuleLesson
        $lesson = new ModuleLesson();
        $lesson->module = $courseModule->id;
        $lesson->name = $request->name;
        $lesson->start_date = $request->start_date;
        $lesson->due_date = $request->due_date;
        $lesson->description = $request->description;
        $lesson->url = $request->url;

        // Save lesson
        $lesson->save();

        // Return success response
        return Response::success([
            'lesson' => $lesson,
        ]);
    }
}