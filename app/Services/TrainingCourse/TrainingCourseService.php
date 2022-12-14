<?php

namespace App\Services\TrainingCourse;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\CourseModule;
use App\Models\ModuleLesson;
use App\Models\TrainingCourse;
use App\Models\User;
use Exception;
use Illuminate\Support\Carbon;

class TrainingCourseService
{
    // Create training course
    public function createTrainingCourse($request)
    {
        // Cast $request->roles to variable
        $roles = $request->roles;

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

        // Loop through $roles array
        foreach ($roles as $role):
            $trainingCourse->roles()->attach($role['role']);
        endforeach;

        $attachedRoles = $trainingCourse->roles;

        foreach ($attachedRoles as $attachedRole):
            $this->attachCourseWithUsers($attachedRole, $trainingCourse);
        endforeach;

        // Return success response
        return Response::success([
            'code' => Response::HTTP_CREATED,
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
        $courseModule->is_exam_required = $request->is_exam_required;

        // Save Module
        $courseModule->save();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_CREATED,
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
            'code' => Response::HTTP_CREATED,
            'lesson' => $lesson,
        ]);
    }

    // Fetch Training Courses
    public function fetchAllTrainingCourses()
    {
        // Get training courses
        $trainingCourses = TrainingCourse::with(['modules.lessons', 'roles', 'enrolledUsers.profile', 'modules' => function ($q) {
            $q->withCount('lessons');
        }, 'enrolledUsers.department'])
            ->withCount('enrolledUsers', 'modules')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'training-courses' => $trainingCourses,
        ]);
    }

    // Fetch single training course
    public function fetchSingleTrainingCourse($request)
    {
        // Get training course
        $trainingCourse = TrainingCourse::where('id', $request->course)
            ->with(['modules.lessons', 'roles', 'enrolledUsers.profile', 'modules' => function ($q) {
                $q->withCount('lessons');
            }, 'enrolledUsers.department'])
            ->withCount('enrolledUsers', 'modules')
            ->firstOrFail();

        // Return success request
        return Response::success([
            'code' => Response::HTTP_OK,
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
            'code' => Response::HTTP_OK,
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
            'code' => Response::HTTP_OK,
            'training-course' => $trainingCourse->with(['modules.lessons', 'roles', 'enrolledUsers.profile', 'modules' => function ($q) {
                $q->withCount('lessons');
            }])
                ->withCount('enrolledUsers', 'modules')
                ->latest('updated_at')
                ->first(),
        ]);
    }

    // Assign user to course
    public function enrollUserToCourse($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Get already assigned courses
        $alreadyAssignedCourses = $user->courses;

        // Initiate empty array for IDs for $alreadyAssignedCourses
        $alreadyAssignedCoursesIds = [];

        // Extract IDs of already assigned courses
        foreach ($alreadyAssignedCourses as $alreadyAssignedCourse):
            array_push($alreadyAssignedCoursesIds, $alreadyAssignedCourse['id']);
        endforeach;

        // Cast $request->courses to variable $courses
        $newTrainingCoursesToAssign = $request->courses;

        // Initiate array for new course ids
        $newTrainingCourseIds = [];

        foreach ($newTrainingCoursesToAssign as $newTrainingCourseToAssign):
            array_push($newTrainingCourseIds, $newTrainingCourseToAssign['course']);
        endforeach;

        // Check if $newTrainingCoursesToAssign array contains ids of courses that are already assigned to the user and return new ids
        $trainingCoursesToBeAssigned = array_diff($newTrainingCourseIds, $alreadyAssignedCoursesIds);

        // Check if id of the already assigned courses are present in the new set of course ids
        $coursesToBeUnassigned = array_diff($alreadyAssignedCoursesIds, $newTrainingCourseIds);

        // Loop through $coursesToBeUnassigned array and unassign courses
        foreach ($coursesToBeUnassigned as $courseToBeUnassigned):
            $user->courses()->detach($courseToBeUnassigned);
        endforeach;

        // Loop through $trainingCoursesToBeAssigned array and assign courses
        foreach ($trainingCoursesToBeAssigned as $trainingCourseToBeAssigned):
            // enroll user to course
            $user->courses()->attach($trainingCourseToBeAssigned);
        endforeach;

        // Return success
        return Response::success([
            'code' => Response::HTTP_OK,
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
            'code' => Response::HTTP_OK,
            'user' => $user->where('id', $user->id)->with('profile', 'courses.modules.lessons')->first(),
        ]);
    }

    // Assign course to users
    public function assignCourseToUsers($request)
    {
        // Get course
        $course = TrainingCourse::findOrFail($request->course);

        // Cast $request->users array to variable
        $alreadyEnrolledUsers = $course->enrolledUsers;

        // Initiate empty array for IDs for $alreadyEnrolledUsers
        $alreadyEnrolledUsersIds = [];

        // Extract IDs of already enrolled users
        foreach ($alreadyEnrolledUsers as $alreadyEnrolledUser):
            array_push($alreadyEnrolledUsersIds, $alreadyEnrolledUser['id']);
        endforeach;

        // Cast $request->users to variable $newUsersToEnroll
        $newUsersToEnroll = $request->users;

        // Initiate array for new user ids
        $newUsersIds = [];

        foreach ($newUsersToEnroll as $newUserToEnroll):
            array_push($newUsersIds, $newUserToEnroll['user']);
        endforeach;

        // Check if $newUsersToEnroll array contains ids of users that are already enrolled to the course and return new ids
        $usersToBeEnrolled = array_diff($newUsersIds, $alreadyEnrolledUsersIds);

        // Check if id of the already assigned courses are present in the new set of course ids
        $usersToBeUnrolled = array_diff($alreadyEnrolledUsersIds, $newUsersIds);

        // Loop through $usersToBeEnrolled array and unassign courses
        foreach ($usersToBeUnrolled as $userToBeUnrolled):
            $course->enrolledUsers()->detach($userToBeUnrolled);
        endforeach;

        // Loop through $trainingCoursesToBeAssigned array and assign courses
        foreach ($usersToBeEnrolled as $userToBeEnrolled):
            // enroll user to course
            $course->enrolledUsers()->attach($userToBeEnrolled);
        endforeach;

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'course' => $course->where('id', $course->id)->with(['modules.lessons', 'enrolledUsers.profile'])->first(),
        ]);
    }

    public function unassignUsersFromCourse($request)
    {
        // Get course
        $course = TrainingCourse::findOrFail($request->course);

        // Cast $request->users array to variable
        $users = $request->users;

        // Loop through $users array
        foreach ($users as $user):
            $course->enrolledUsers()->detach($user['user']);
        endforeach;

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'course' => $course->where('id', $course->id)->with(['modules.lessons', 'enrolledUsers.profile'])->first(),
        ]);
    }

    // Assign user to course
    public function assignUsersToCourse($request)
    {
        // Get course
        $course = TrainingCourse::findOrFail($request->course);

        // Cast $request->users array to variable
        $users = $request->users;

        // Cast $course->alreadyAssignedToCourse($users) to variable
        $usersAlreadyEnrolled = $course->alreadyAssignedToCourse($users);

        // Check if any of the user in $users is already enrolled to the $course
        if ($usersAlreadyEnrolled !== false) {
            // Casting to variable
            $alreadyEnrolledUsers = implode(', ', $usersAlreadyEnrolled);

            // Getting count of the invalid options
            $usersCount = count(explode(', ', $alreadyEnrolledUsers));

            // Building test according the $optionCount
            $enrolledUserText = $usersCount > 1 ? 'Users ' . $alreadyEnrolledUsers . ' are' : 'User ' . $alreadyEnrolledUsers . ' is';

            throw new Exception(ResponseMessage::customMessage($enrolledUserText . ' already enrolled in course ' . $course->id));
        }

        // Start date
        $startDate = Carbon::now();

        // Loop through $users array
        foreach ($users as $user):
            $course->enrolledUsers()->attach($user['user'], [
                'start_date' => $startDate->format('Y-m-d'),
                'due_date' => $startDate->addMonths(3)->format('Y-m-d'),
            ]);
        endforeach;

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'course' => $course->where('id', $course->id)->with(['modules.lessons', 'enrolledUsers.profile', 'enrolledUsers' => function ($q) {
                $q->withPivot('start_date', 'due_date');
            }])->first(),
        ]);

    }

    // Get user for the roles and attach the course to those users
    private function attachCourseWithUsers($role, $course)
    {
        $usersByRole = User::whereHas('roles', function ($q) use ($role) {
            $q->where('role_id', $role->id);
        })->get();

        foreach ($usersByRole as $userByRole):
            // Start date
            $startDate = Carbon::now();

            $userByRole->courses()->attach($course->id, [
                'start_date' => $startDate->format('Y-m-d'),
                'due_date' => $startDate->addMonths(3)->format('Y-m-d'),
            ]);
        endforeach;
    }
}