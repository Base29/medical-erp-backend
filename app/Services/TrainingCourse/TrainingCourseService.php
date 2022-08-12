<?php

namespace App\Services\TrainingCourse;

use App\Helpers\Response;
use App\Helpers\UpdateService;
use App\Models\CourseModule;
use App\Models\ModuleLesson;
use App\Models\TrainingCourse;
use App\Models\User;

class TrainingCourseService
{
    // Create training course
    public function createTrainingCourse($request)
    {
        // Initiate instance of TrainingCourse model
        $trainingCourse = new TrainingCourse();
        $trainingCourse->name = $request->name;
        $trainingCourse->description = $request->description;
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
            'training-course' => $trainingCourse->with('modules.lessons', 'roles', 'enrolledUsers.profile')->latest()->first(),
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

    // Fetch Training Courses
    public function fetchAllTrainingCourses()
    {
        // Get training courses
        $trainingCourses = TrainingCourse::with('modules.lessons', 'roles', 'enrolledUsers.profile')->paginate(10);

        // Return success response
        return Response::success([
            'training-courses' => $trainingCourses,
        ]);
    }

    // Fetch single training course
    public function fetchSingleTrainingCourse($request)
    {
        // Get training course
        $trainingCourse = TrainingCourse::where('id', $request->course)
            ->with('modules.lessons', 'roles', 'enrolledUsers.profile')
            ->firstOrFail();

        // Return success request
        return Response::success([
            'training-course' => $trainingCourse,
        ]);
    }

    // Delete training course(
    public function deleteTrainingCourse($request)
    {
        // Get training course
        $trainingCourse = TrainingCourse::where('id', $request->course)->with('modules.lessons', 'roles', 'enrolledUsers.profile')->firstOrFail();

        // Delete training course
        $trainingCourse->delete();

        // Return success response
        return Response::success([
            'training-course' => $trainingCourse,
        ]);
    }

    // Update training course
    public function updateTrainingCourse($request)
    {
        // Get Training course
        $trainingCourse = TrainingCourse::where('id', $request->course)->firstOrFail();

        // Update training course
        UpdateService::updateModel($trainingCourse, $request->validated(), 'course');

        // Return success response
        return Response::success([
            'training-course' => $trainingCourse->with('modules.lessons', 'roles', 'enrolledUsers.profile')->latest('updated_at')->first(),
        ]);
    }

    // Assign user to course
    public function enrollUserToCourse($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Cast $request->courses to variable $courses
        $trainingCourses = $request->courses;

        // Loop through $courses array
        foreach ($trainingCourses as $trainingCourse):
            // enroll user to course
            $user->courses()->attach($trainingCourse['course']);
        endforeach;

        // Return success
        return Response::success([
            'user' => $user->where('id', $user->id)->with('profile', 'courses.modules.lessons')->first(),
        ]);
    }

    // Unroll user from course
    public function unrollUserFromCourse($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Cast $request->courses to variable $courses
        $trainingCourses = $request->courses;

        // Loop through $courses array
        foreach ($trainingCourses as $trainingCourse):
            // enroll user to course
            $user->courses()->detach($trainingCourse['course']);
        endforeach;

        // Return success
        return Response::success([
            'user' => $user->where('id', $user->id)->with('profile', 'courses.modules.lessons')->first(),
        ]);
    }
}