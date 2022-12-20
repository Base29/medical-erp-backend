<?php
namespace App\Services\User;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\Applicant;
use App\Models\ContractSummary;
use App\Models\CourseModule;
use App\Models\CourseModuleExam;
use App\Models\CourseProgress;
use App\Models\Department;
use App\Models\HiringRequest;
use App\Models\LessonProgress;
use App\Models\LocumSession;
use App\Models\LocumSessionInvite;
use App\Models\MiscellaneousInformation;
use App\Models\ModuleLesson;
use App\Models\ModuleProgress;
use App\Models\Policy;
use App\Models\PositionSummary;
use App\Models\Practice;
use App\Models\Profile;
use App\Models\Role;
use App\Models\TrainingCourse;
use App\Models\User;
use App\Notifications\Locum\ChangeLocumStatusNotification;
use App\Notifications\User\CandidateHiredNotification;
use App\Notifications\WelcomeNewEmployeeNotification;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    // Create user
    public function createUser($request)
    {
        // Check if the user being created is a candidate
        if ($request->is_candidate) {
            $requiredFields = [
                'gender',
                'mobile_phone',
                'job_title',
                'contract_type',
                'contract_start_date',
                'contracted_hours_per_week',
                'hiring_request',
                'department',
            ];

            if (!$request->hasAny($requiredFields)) {
                throw new Exception(
                    ResponseMessage::customMessage('Candidate must have all the required fields ' . implode(' | ', $requiredFields)),
                    Response::HTTP_BAD_REQUEST
                );
            }

            // Get hiring request
            $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

            // Get Department
            $department = Department::findOrFail($request->department);
        }

        // Check if the user is not a candidate so the password field is required
        if (!$request->is_candidate && (!$request->has('password') || !$request->has('password_confirmation'))) {
            throw new Exception(ResponseMessage::customMessage('The password and password_confirmation fields are required'), Response::HTTP_BAD_REQUEST);
        }

        // // Initiating a null variable for profile image
        // $profileImage = null;

        // // Check if the profile_image is present and filled
        // if ($request->has('profile_image') || $request->filled('profile_image')) {
        //     // Upload user profile picture
        //     $url = FileUploadService::upload($request->file('profile_image'), 'profileImages', 's3');

        //     // Assigning value of $url to $profileImage
        //     $profileImage = $url;
        // }

        // Generating a random password for the candidate
        $random = Str::random(40);

        // Create user
        $user = new User();
        $user->email = $request->email;
        $user->password = Hash::make($request->is_candidate ? $random : $request->password);
        $user->is_active = $request->is_candidate ? Config::get('constants.USER.INACTIVE') : Config::get('constants.USER.ACTIVE');
        $user->is_candidate = $request->is_candidate ? Config::get('constants.USER.CANDIDATE') : Config::get('constants.USER.NOT_CANDIDATE');
        $user->department_id = $request->is_candidate ? $department->id : null;
        $user->generic_user = $request->generic_user ? $request->generic_user : null;
        $user->save();

        // Create profile for the user
        $profile = new Profile();
        $profile->first_name = $request->first_name;
        $profile->last_name = $request->last_name;
        $profile->gender = $request->is_candidate ? $request->gender : null;
        $profile->mobile_phone = $request->is_candidate ? $request->mobile_phone : null;
        $profile->primary_role = $request->is_candidate ? $request->job_title : null;
        // $profile->hiring_request_id = $request->is_candidate ? $hiringRequest->id : null;
        $profile->hiring_request_id = isset($hiringRequest) && $request->is_candidate ? $hiringRequest->id : null;
        $user->profile()->save($profile);

        // Create position summary
        $positionSummary = new PositionSummary();
        $positionSummary->job_title = $request->is_candidate ? $request->job_title : null;
        $positionSummary->contract_type = $request->is_candidate ? $request->contract_type : null;
        $user->positionSummary()->save($positionSummary);

        // Parsing date format to Y-m-d
        $formattedDate = Carbon::parse($request->contract_start_date)
            ->format('Y-m-d');

        // Create contract summary
        $contractSummary = new ContractSummary();
        $contractSummary->contract_start_date = $request->is_candidate ? $request->contract_start_date : null;
        $contractSummary->contracted_hours_per_week = $request->is_candidate ? $request->contracted_hours_per_week : null;
        $user->contractSummary()->save($contractSummary);

        // Create misc info
        $miscInfo = new MiscellaneousInformation();
        $miscInfo->job_specification = null;
        $user->miscInfo()->save($miscInfo);

        // // Create education
        // $education = new Education();
        // $education->institution = null;
        // $user->education()->save($education);

        // // Create employment history
        // $employmentHistory = new EmploymentHistory();
        // $employmentHistory->employer_name = null;
        // $user->employmentHistories()->save($employmentHistory);

        // // Create reference
        // $reference = new Reference();
        // $reference->reference_type = null;
        // $user->references()->save($reference);

        // // Create legal
        // $legal = new Legal();
        // $legal->name = null;
        // $user->legal()->save($legal);

        // Add user as a applicant to the hiring request
        if (isset($hiringRequest)) {
            // Instance of Applicant
            $applicant = new Applicant();
            $applicant->hiring_request_id = $hiringRequest->id;
            $applicant->user_id = $user->id;

            // Save applicant
            $applicant->save();
        }

        // Assigning role(s) if user being created is a candidate
        if (isset($hiringRequest) && $user->is_candidate) {
            // Assigning primary role (position) to user
            $user->assignRole($hiringRequest->role);

            // Check if request has additional_roles array
            if ($request->has('additional_roles')) {
                // Assigning additional roles to user
                foreach ($request->additional_roles as $additional_role) {
                    $user->assignRole($additional_role);
                }
            }
        }

        return Response::success([
            'code' => Response::HTTP_CREATED,
            'user' => $user
                ->with('profile.hiringRequest', 'positionSummary', 'contractSummary', 'roles', 'practices')
                ->latest()
                ->first(),
        ]);
    }

    // Delete user
    public function deleteUser($id)
    {
        // Check if the user exists with the provided $id
        $user = User::findOrFail($id);

        if (!$user) {
            throw new Exception(ResponseMessage::notFound('User', $id, false), Response::HTTP_NOT_FOUND);
        }

        // Delete user with the provided $id
        $user->delete();

        return Response::success([
            'code' => Response::HTTP_OK,
            'user' => $user,
        ]);
    }

    // Fetch users
    public function fetchUsers($request)
    {

        // Check if $request->filter exists
        if ($request->has('filter')) {

            // Allowed search filters
            $allowedFilters = [
                'mobile_phone',
                'last_name',
                'email',
                'role',
                'is_active',
                'is_candidate',
                'is_hired',
                'is_locum',
            ];

            // Check if $request->filter === $allowedFilters
            $filterIsAllowed = in_array($request->filter, $allowedFilters);

            if (!$filterIsAllowed) {
                throw new Exception(ResponseMessage::allowedFilters($allowedFilters), Response::HTTP_BAD_REQUEST);
            }

            if ($request->filter === 'mobile_phone' || $request->filter === 'last_name') {
                // Filter users by mobile_phone or last_name
                $users = User::with(
                    'profile',
                    'positionSummary',
                    'contractSummary',
                    'roles',
                    'practices',
                    'employmentCheck',
                    'workPatterns.workTimings',
                    'locumNotes',
                    'qualifications',
                    'interviewSchedules.interviewMiscInfo',
                    'interviewSchedules.interviewScore'
                )
                    ->whereHas('profile', function ($q) {
                        $q->where(request()->filter, request()->value);
                    })
                    ->latest()
                    ->paginate($request->per_page ? $request->per_page : 10);

            } elseif ($request->filter === 'email' || $request->filter === 'is_active' || $request->filter === 'is_candidate' || $request->filter === 'is_hired' || $request->filter === 'is_locum') {
                // Filter users by email
                $users = User::where($request->filter, $request->value)->with(
                    'profile',
                    'positionSummary',
                    'contractSummary',
                    'roles',
                    'practices',
                    'employmentCheck',
                    'workPatterns.workTimings',
                    'locumNotes',
                    'qualifications',
                    'interviewSchedules.interviewMiscInfo',
                    'interviewSchedules.interviewScore'
                )
                    ->latest()
                    ->paginate($request->per_page ? $request->per_page : 10);

            } elseif ($request->filter === 'role') {

                // Filter users by role
                $users = User::with(
                    'profile',
                    'positionSummary',
                    'contractSummary',
                    'roles',
                    'practices',
                    'employmentCheck',
                    'workPatterns.workTimings',
                    'locumNotes',
                    'qualifications',
                    'interviewSchedules.interviewMiscInfo',
                    'interviewSchedules.interviewScore'
                )
                    ->whereHas('roles', function ($q) use ($request) {
                        $valueType = gettype($request->value);

                        if ($valueType === 'integer'):
                            // Search by role id
                            $q->where('id', $request->value);
                        else:
                            // search by role name
                            $q->where('name', $request->value);
                        endif;

                    })
                    ->latest()
                    ->paginate($request->per_page ? $request->per_page : 10);
            }

        } else {
            // Fetching all the users from database
            $users = User::with(
                'profile',
                'positionSummary',
                'contractSummary',
                'roles',
                'practices',
                'employmentCheck',
                'workPatterns.workTimings',
                'locumNotes',
                'qualifications',
                'interviewSchedules.interviewMiscInfo',
                'interviewSchedules.interviewScore'
            )
                ->latest()
                ->paginate($request->per_page ? $request->per_page : 10);
        }

        return Response::success([
            'code' => Response::HTTP_OK,
            'users' => $users,
        ]);
    }

    // Update user
    public function updateUser($request)
    {
        // Allowed fields when updating a task
        $allowedFields = [
            'first_name',
            'last_name',
            'profile_image',
            'gender',
            'email_professional',
            'mobile_phone',
            'dob',
            'address_line_1',
            'address_line_2',
            'city',
            'county',
            'country',
            'zip_code',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new Exception(ResponseMessage::allowedFields($allowedFields), Response::HTTP_BAD_REQUEST);
        }

        // Fetch User
        $user = User::findOrFail($request->user);

        // Get profile for the user
        $profile = Profile::where('user_id', $user->id)->firstOrFail();

        UpdateService::updateModel($profile, $request->validated(), 'user');

        return Response::success([
            'code' => Response::HTTP_OK,
            'user' => $profile::with('user', 'user.positionSummary', 'user.contractSummary', 'user.roles', 'user.practices', 'user.workPatterns.workTimings', 'user.locumNotes', 'user.qualifications')
                ->latest('updated_at')
                ->first(),
        ]);

    }

    // Me
    public function me()
    {
        // Get ID of the logged in user
        $authenticatedUser = auth()->user()->id;

        // Get user from database
        $user = User::where('id', $authenticatedUser)
            ->with('profile.hiringRequest', 'positionSummary', 'contractSummary', 'roles', 'practices', 'employmentCheck', 'workPatterns.workTimings', 'locumNotes', 'qualifications')
            ->withCount(['courses', 'overdueCourses', 'completedCourses', 'inProgressCourses'])
            ->firstOrFail();

        // Return details of the user
        return Response::success([
            'code' => Response::HTTP_OK,
            'user' => $user,
        ]);
    }

    // Fetch single user
    public function fetchSingleUser($request)
    {
        // Get user from database
        $user = User::where('id', $request->user)
            ->with([
                'profile.applicant',
                'positionSummary',
                'contractSummary',
                'roles',
                'practices',
                'employmentCheck',
                'workPatterns.workTimings',
                'courses.modules.lessons',
                'locumNotes',
                'qualifications',
                'interviewSchedules.interviewScore',
            ])
            ->withCount(['courses', 'overdueCourses', 'completedCourses', 'inProgressCourses'])
            ->firstOrFail();

        // Return details of the user
        return Response::success([
            'code' => Response::HTTP_OK,
            'user' => $user,
        ]);
    }

    // Generate password for candidate
    public function hireCandidate($request)
    {
        // Get user
        $candidate = User::where('id', $request->candidate)
            ->with('profile')
            ->firstOrFail();

        // Get Applicant
        $applicant = Applicant::where('user_id', $candidate->id)->firstOrFail();

        // Check if candidate is hired
        if (!$candidate->is_candidate) {
            throw new Exception(ResponseMessage::customMessage('User ' . $candidate->id . ' is not a candidate'), Response::HTTP_FORBIDDEN);
        }

        // Check if $candidate is already hired
        if ($candidate->is_hired) {
            throw new Exception(ResponseMessage::customMessage('User ' . $candidate->id . ' is already hired'), Response::HTTP_CONFLICT);
        }

        // Check applicant status
        if ($candidate->applicant_status === 0 || $candidate->applicant_status === null) {
            throw new Exception(ResponseMessage::customMessage('Candidate cannot be hired. The candidate have not cleared the interview process.'), Response::HTTP_CONFLICT);
        }

        // Fetch hiring request
        $hiringRequest = HiringRequest::where('id', $candidate->profile->hiring_request_id)->firstOrFail();

        // Hiring Manager
        $hiringManager = User::where('id', $hiringRequest->notifiable)->firstOrFail();

        // Generate password
        $password = Str::random(16);

        // Save user pass and make user active
        $candidate->password = Hash::make($password);
        $candidate->is_hired = Config::get('constants.USER.HIRED');
        $candidate->is_active = Config::get('constants.USER.ACTIVE');
        $candidate->update();

        // Update status in applicants table
        $applicant->status = Config::get('constants.USER.APPLICANT_STATUS.ACCEPTED');
        $applicant->save();

        // Assign permissions
        $candidate->givePermissionTo([
            'can_manage_own_profile',
            'can_manage_own_trainings',
            'can_manage_own_locum_sessions',
        ]);

        // Assign work pattern
        $candidate->workPatterns()->attach($hiringRequest->workPatterns[0]->id);
        $candidate->practices()->attach($hiringRequest->practice->id, [
            'type' => 'user',
        ]);

        // Assign courses as per $hiringRequest->role
        $this->assignCoursesToCandidateWhenHired($hiringRequest->role, $candidate);

        $credentials = [
            'email' => $candidate->email,
            'password' => $password,
        ];

        $candidate->notify(new WelcomeNewEmployeeNotification($credentials));
        $hiringManager->notify(new CandidateHiredNotification(
            $hiringRequest,
            $hiringManager,
            $candidate
        ));

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'candidate' => $candidate,
        ]);
    }

    // Record lesson progress
    public function recordLessonProgress($request)
    {
        // Get authenticated user
        $authenticatedUser = auth()->user();

        // Get lesson
        $lesson = ModuleLesson::where('id', $request->lesson)->with('module')->firstOrFail();

        // Module
        $module = CourseModule::where('id', $lesson->module)->with('course')->firstOrFail();

        // Check if $authenticatedUser already recorded progress

        $lessonProgress = new LessonProgress();

        if ($lessonProgress->alreadyRecordedProgress($lesson->id, $authenticatedUser->id)) {
            throw new Exception(ResponseMessage::customMessage('You have already recorded progress for this lesson'), Response::HTTP_CONFLICT);
        }

        // Lesson completion evidence folder path on S3
        $folderPath = 'user-' . $authenticatedUser->id . '/trainings/course-' . $module->course . '/module-' . $lesson->module . '/lesson-' . $lesson->id;

        // Completion evidence
        $completionEvidenceUrl = FileUploadService::upload($request->completion_evidence, $folderPath, 's3');

        // Save progress
        $lessonProgress->lesson = $lesson->id;
        $lessonProgress->user = $authenticatedUser->id;
        $lessonProgress->completed_at = $request->completed_at;
        $lessonProgress->is_completed = $request->is_completed;
        $lessonProgress->completion_evidence = $completionEvidenceUrl;
        $lessonProgress->save();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'lesson-progress' => $lessonProgress,
        ]);
    }

    // Fetch user training courses
    public function fetchUserTrainingCourses()
    {
        // Get user
        $authenticatedUser = auth()->user()->id;

        // Get user courses
        $userCourses = User::where('id', $authenticatedUser)
            ->with(['profile', 'courses'])
            ->withCount(['courses', 'overdueCourses', 'completedCourses', 'inProgressCourses'])
            ->paginate(10);

        return Response::success([
            'code' => Response::HTTP_OK,
            'user-courses' => $userCourses,
        ]);
    }

    // Record module progress
    public function recordModuleProgress($request)
    {
        // Get authenticated user
        $authenticatedUser = auth()->user();

        // Module
        $module = CourseModule::where('id', $request->module)->with('course')->firstOrFail();

        // Check if $authenticatedUser has already recorded progress
        $moduleProgress = new ModuleProgress();

        if ($moduleProgress->alreadyRecordedProgress($module->id, $authenticatedUser->id)) {
            throw new Exception(ResponseMessage::customMessage('You have already recorded progress for this module'), Response::HTTP_CONFLICT);
        }

        // Module completion evidence folder path on S3
        $folderPath = 'user-' . $authenticatedUser->id . '/trainings/course-' . $module->course . '/module-' . $module->id;

        // Completion evidence
        $completionEvidenceUrl = FileUploadService::upload($request->completion_evidence, $folderPath, 's3');

        // Save progress
        $moduleProgress->module = $module->id;
        $moduleProgress->user = $authenticatedUser->id;
        $moduleProgress->completed_at = $request->completed_at;
        $moduleProgress->is_completed = $request->is_completed;
        $moduleProgress->completion_evidence = $completionEvidenceUrl;
        $moduleProgress->save();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'module-progress' => $moduleProgress,
        ]);
    }

    // Record course progress
    public function recordCourseProgress($request)
    {
        // Get authenticated user
        $authenticatedUser = auth()->user();

        // Course
        $course = TrainingCourse::where('id', $request->course)->firstOrFail();

        // Check if $authenticatedUser has already recorded progress
        $courseProgress = new CourseProgress();

        if ($courseProgress->alreadyRecordedProgress($course->id, $authenticatedUser->id)) {
            throw new Exception(ResponseMessage::customMessage('You have already recorded progress for this course'), Response::HTTP_CONFLICT);
        }

        // Check if $course is assigned to user
        if (!$course->isAssignedToCourse($authenticatedUser->id)) {
            throw new Exception(ResponseMessage::customMessage('You are not allowed to record progress for this course.'));
        }

        // Module completion evidence folder path on S3
        $folderPath = 'user-' . $authenticatedUser->id . '/trainings/course-' . $course->id;

        // Completion evidence
        $completionEvidenceUrl = FileUploadService::upload($request->completion_evidence, $folderPath, 's3');

        // Save progress
        $courseProgress->course = $course->id;
        $courseProgress->user = $authenticatedUser->id;
        $courseProgress->completed_at = $request->completed_at;
        $courseProgress->is_completed = $request->is_completed;
        $courseProgress->completion_evidence = $completionEvidenceUrl;
        $courseProgress->save();

        // Change in progress status in training_course_user pivot table
        $this->updateInProgressStatusForCourse($authenticatedUser, $course);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'module-progress' => $courseProgress,
        ]);
    }

    // Record end of module exam
    public function createEndOfModuleExam($request)
    {
        // Get module
        $module = CourseModule::findOrFail($request->module);

        // Get authenticated user
        $authenticatedUser = auth()->user();

        // Initiate instance of CourseModuleExam
        $moduleExam = new CourseModuleExam();
        $moduleExam->module = $module->id;
        $moduleExam->user = $authenticatedUser->id;
        $moduleExam->type = $request->type;
        $moduleExam->number_of_questions = $request->number_of_questions;
        $moduleExam->is_restricted = $request->is_restricted;
        $moduleExam->duration = $request->duration;
        $moduleExam->description = $request->description;
        $moduleExam->url = $request->url;
        $moduleExam->is_passing_percentage = $request->is_passing_percentage;
        $moduleExam->passing_percentage = $request->passing_percentage;
        $moduleExam->is_passed = $request->is_passed;
        $moduleExam->grade_achieved = $request->grade_achieved;
        $moduleExam->percentage_achieved = $request->percentage_achieved;

        // Save module exam
        $moduleExam->save();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_CREATED,
            'module-exam' => $moduleExam,
        ]);
    }

    // Get single enrolled course
    public function fetchSingleEnrolledCourse($request)
    {
        // Get authenticated user
        $authenticatedUser = auth()->user();

        $isUserEnrolledToCourse = $authenticatedUser->courses->contains($request->course);

        if (!$isUserEnrolledToCourse) {
            throw new Exception(ResponseMessage::customMessage('User is not enrolled to the provided course'), Response::HTTP_BAD_REQUEST);
        }

        // Get user courses
        $userCourse = TrainingCourse::where('id', $request->course)
            ->with(['modules.lessons', 'modules.moduleProgress' => function ($q) use ($authenticatedUser) {
                $q->where('user', $authenticatedUser->id);
            }, 'modules.lessons.lessonProgress' => function ($q) use ($authenticatedUser) {
                $q->where('user', $authenticatedUser->id);
            }, 'courseProgress' => function ($q) use ($authenticatedUser) {
                $q->where('user', $authenticatedUser->id);
            }, 'modules' => function ($q) {
                $q->withCount('lessons');
            }])
            ->withCount('modules')
            ->firstOrFail();

        return Response::success([
            'code' => Response::HTTP_OK,
            'user-course' => $userCourse,
        ]);
    }

    // Get hired users
    public function fetchEmployees()
    {
        // Get hired users
        $employees = User::where('is_hired', 1)
            ->with(['profile', 'department', 'courses.modules.lessons', 'courses' => function ($q) {
                $q->with(['modules' => function ($q) {
                    $q->withCount('lessons');
                }])->withCount('modules');
            }])
            ->withCount('courses')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'employees' => $employees,
        ]);
    }

    // Search candidate profiles
    public function searchCandidateProfiles($request)
    {
        if ($request->has('filter')):

            // Check if value is being sent
            if (!$request->has('value')) {
                throw new Exception(ResponseMessage::customMessage('Field value is required'), Response::HTTP_BAD_REQUEST);
            }

            /**
             * Filters with the value as integer
             *
             * Add new filters to $filtersWithIntValue array to allow them to be searched
             */
            $filtersWithIntValue = [
                'role',
                'location',
                'hiring_request',
            ];

            /**
             * Filters with the value of string
             *
             * Add new filters to $filtersWithStringValue array to allow them to be searched
             */
            $filtersWithStringValue = [
                'first_name',
                'last_name',
                'email',
            ];

            // Get the type of $request->lcg_value
            $valueType = gettype($request->value);

            // Cast $request->filter to variable
            $filter = $request->filter;

            // Fetching results for $filtersWithIntValue
            if (in_array($filter, $filtersWithIntValue)):

                // Check the type of $request->value is integer
                if ($valueType !== 'integer') {
                    throw new Exception(ResponseMessage::customMessage('The value for the filter "' . $filter . '" should be of type integer'), Response::HTTP_CONFLICT);
                }

                switch ($filter) {
                    case 'role':

                        // Check if role exists
                        $role = Role::findOrFail($request->value);

                        // Getting candidates filtered by role
                        $filteredCandidates = User::with('profile.hiringRequest', 'positionSummary', 'contractSummary', 'roles', 'practices', 'employmentCheck', 'workPatterns.workTimings', 'locumNotes', 'qualifications')
                            ->whereHas('roles', function ($q) use ($role) {
                                $q->where('id', $role->id);
                            })
                            ->where('is_candidate', 1)
                            ->latest()
                            ->paginate(10);
                        break;

                    case 'location':
                        // Check if location(practice) exists
                        $location = Practice::findOrFail($request->value);

                        // Getting candidates filtered by hiring request
                        $filteredCandidates = User::where('is_candidate', 1)
                            ->whereHas('profile', function ($q) use ($location) {
                                $q->whereHas('hiringRequest', function ($q) use ($location) {
                                    $q->where('practice_id', $location->id);
                                });
                            })->with([
                            'profile.hiringRequest',
                            'positionSummary',
                            'contractSummary',
                            'roles',
                            'practices',
                            'employmentCheck',
                            'workPatterns.workTimings',
                            'locumNotes',
                            'qualifications',
                        ])
                            ->latest()
                            ->paginate(10);

                        break;

                    case 'hiring_request':
                        // Check if the hiring request exists
                        $hiringRequest = HiringRequest::findOrFail($request->value);

                        // Getting candidates filtered by hiring request
                        $filteredCandidates = User::where('is_candidate', 1)
                            ->whereHas('profile', function ($q) use ($hiringRequest) {
                                $q->where('hiring_request_id', $hiringRequest->id);
                            })->with([
                            'profile.hiringRequest',
                            'positionSummary',
                            'contractSummary',
                            'roles',
                            'practices',
                            'employmentCheck',
                            'workPatterns.workTimings',
                            'locumNotes',
                            'qualifications',
                        ])
                            ->latest()
                            ->paginate(10);
                        break;
                }
            endif;

            if (in_array($filter, $filtersWithStringValue)):

                // Check the type of $request->value is string
                if ($valueType !== 'string') {
                    throw new Exception(ResponseMessage::customMessage('The value for the filter "' . $filter . '" should be of type string'), Response::HTTP_CONFLICT);
                }

                switch ($filter) {
                    case 'email':

                        $filteredCandidates = User::where(['is_candidate' => 1, 'email' => $request->value])
                            ->with([
                                'profile.hiringRequest',
                                'positionSummary',
                                'contractSummary',
                                'roles',
                                'practices',
                                'employmentCheck',
                                'workPatterns.workTimings',
                                'locumNotes',
                                'qualifications',
                            ])
                            ->latest()
                            ->paginate(10);
                        break;

                    case 'first_name':
                        $filteredCandidates = User::whereHas('profile', function ($q) use ($request) {
                            $q->where('first_name', 'like', '%' . $request->value . '%');
                        })
                            ->where('is_candidate', 1)
                            ->with([
                                'profile.hiringRequest',
                                'positionSummary',
                                'contractSummary',
                                'roles',
                                'practices',
                                'employmentCheck',
                                'workPatterns.workTimings',
                                'locumNotes',
                                'qualifications',
                            ])
                            ->latest()
                            ->paginate(10);
                        break;

                    case 'last_name':
                        $filteredCandidates = User::whereHas('profile', function ($q) use ($request) {
                            $q->where('last_name', 'like', '%' . $request->value . '%');
                        })
                            ->where('is_candidate', Config::get('constants.USER.CANDIDATE'))
                            ->with([
                                'profile.hiringRequest',
                                'positionSummary',
                                'contractSummary',
                                'roles',
                                'practices',
                                'employmentCheck',
                                'workPatterns.workTimings',
                                'locumNotes',
                                'qualifications',
                            ])
                            ->latest()
                            ->paginate(10);
                        break;

                    default:
                        return false;
                }

            endif;
        else:
            // Fetch all Candidates
            $filteredCandidates = User::where('is_candidate', Config::get('constants.USER.CANDIDATE'))
                ->with([
                    'profile.hiringRequest',
                    'positionSummary',
                    'contractSummary',
                    'roles',
                    'practices',
                    'employmentCheck',
                    'workPatterns.workTimings',
                    'locumNotes',
                    'qualifications',
                ])
                ->latest()
                ->paginate(10);
        endif;

        // Return response
        return Response::success([
            'code' => Response::HTTP_OK,
            'filtered-candidates' => $filteredCandidates,
        ]);

    }

    // Make user locum
    public function updateUserLocumStatus($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Switch Case
        switch ($request->locum_status) {
            case 1:

                // Check if the user already a locum
                if ($user->isLocum()) {
                    throw new Exception(ResponseMessage::customMessage('User is already a locum'), Response::HTTP_CONFLICT);
                }

                // Check if user is active and is a candidate and is hired
                if (!$user->is_active || !$user->is_candidate || !$user->is_hired) {
                    throw new Exception(ResponseMessage::customMessage('User must be active, must be a candidate and must be hired.'), Response::HTTP_CONFLICT);
                }

                // Make user as locum
                $user->is_locum = Config::get('constants.USER.LOCUM');
                $user->update();

                break;

            case 0:
                // Check if the user already a locum
                if (!$user->isLocum()) {
                    throw new Exception(ResponseMessage::customMessage('User is not a locum'), Response::HTTP_CONFLICT);
                }

                // Make user as locum
                $user->is_locum = Config::get('constants.USER.NOT_LOCUM');
                $user->update();

                break;

            default:
                return false;
        }

        $user->notify(new ChangeLocumStatusNotification($user));

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'user' => $user->where('id', $user->id)
                ->with([
                    'profile.applicant',
                    'positionSummary',
                    'contractSummary',
                    'roles',
                    'practices',
                    'employmentCheck',
                    'workPatterns.workTimings',
                    'courses.modules.lessons',
                    'locumNotes',
                    'qualifications',
                ])
                ->first(),
        ]);
    }

    public function filterUsers($request)
    {
        // Query users
        $usersQuery = User::query();

        // Check filters
        if ($request->has('mobile_phone')) {

            $usersQuery = $usersQuery->whereHas('profile', function ($q) use ($request) {
                $q->where('mobile_phone', $request->mobile_phone);
            });
        }

        if ($request->has('first_name')) {

            $usersQuery = $usersQuery->whereHas('profile', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->first_name . '%');
            });
        }

        if ($request->has('last_name')) {
            $usersQuery = $usersQuery->whereHas('profile', function ($q) use ($request) {
                $q->where('last_name', 'like', '%' . $request->last_name . '%');
            });
        }

        if ($request->has('email')) {
            $usersQuery = $usersQuery->where('email', $request->email);
        }

        if ($request->has('role')) {

            if ($request->has('roles')) {
                throw new Exception(ResponseMessage::customMessage('You can either search by roles or role separately'), Response::HTTP_CONFLICT);
            }

            $usersQuery = $usersQuery->whereHas('roles', function ($q) use ($request) {
                $q->where('id', $request->role);
            });
        }

        if ($request->has('is_active')) {
            $usersQuery = $usersQuery->where('is_active', $request->is_active);
        }

        if ($request->has('is_candidate')) {
            $usersQuery = $usersQuery->where('is_candidate', $request->is_candidate);
        }

        if ($request->has('is_hired')) {
            $usersQuery = $usersQuery->where('is_hired', $request->is_hired);
        }

        if ($request->has('is_locum')) {
            $usersQuery = $usersQuery->where('is_locum', $request->is_locum);
        }

        if ($request->has('is_blacklisted')) {
            $usersQuery = $usersQuery->where('is_blacklisted', $request->is_blacklisted);
        }

        if ($request->has('location')) {

            if ($request->has('locations')) {
                throw new Exception(ResponseMessage::customMessage('You can either search by locations or location separately'), Response::HTTP_CONFLICT);
            }

            $usersQuery = $usersQuery->whereHas('practices', function ($q) use ($request) {
                $q->where('practice_id', $request->location);
            });
        }

        if ($request->has('roles')) {

            if ($request->has('role')) {
                throw new Exception(ResponseMessage::customMessage('You can either search by roles or role separately'), Response::HTTP_CONFLICT);
            }

            $usersQuery = $usersQuery->whereHas('roles', function ($q) use ($request) {
                $q->whereIn('id', $request->roles);
            });
        }

        if ($request->has('locations')) {

            if ($request->has('location')) {
                throw new Exception(ResponseMessage::customMessage('You can either search by locations or location separately'), Response::HTTP_CONFLICT);
            }

            $usersQuery = $usersQuery->whereHas('practices', function ($q) use ($request) {
                $q->where('practice_id', $request->locations);
            });
        }

        // If $request has applicant_status
        if ($request->has('applicant_status')) {
            $usersQuery = $usersQuery->where('applicant_status', $request->applicant_status);
        }

        // If $request has offer_status
        if ($request->has('offer_status')) {
            // Filter users by offer status
            $usersQuery = $usersQuery->whereHas('offers', function ($q) use ($request) {
                $q->where(['is_active' => 1, 'status' => $request->offer_status]);
            });
        }

        // If $request has induction_status
        if ($request->has('induction_status')) {
            // Filter users by induction status
            $usersQuery = $usersQuery->whereHas('inductionSchedule', function ($q) use ($request) {
                $q->where('is_completed', $request->induction_status);
            });
        }

        $filteredUsers = $usersQuery->with([
            'profile.applicant',
            'positionSummary',
            'contractSummary',
            'roles',
            'practices',
            'employmentCheck',
            'workPatterns.workTimings',
            'courses.modules.lessons',
            'locumNotes',
            'qualifications',
            'locumSessions' => function ($q) {
                $q->latest();
            },
            'interviewSchedules.interviewMiscInfo',
            'interviewSchedules.interviewScore',
            'offers' => function ($q) {
                $q->orderBy('id', 'desc')->limit(1);
            },
            'offers.amendments',
        ])
            ->latest()
            ->paginate(10);

        // Return response
        return Response::success([
            'code' => Response::HTTP_OK,
            'users' => $filteredUsers,
        ]);
    }

    // Fetch session invites of user
    public function fetchUserSessionInvites($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Get session invites of $user
        $sessionInvites = LocumSessionInvite::where('locum', $user->id)
            ->latest()
            ->get();

        // Return response
        return Response::success([
            'code' => Response::HTTP_OK,
            'session-invites' => $sessionInvites,
        ]);
    }

    // Fetch All Sessions
    public function fetchUserSessions($request)
    {
        // Authenticated user
        $authenticatedUser = auth()->user();

        // Query build
        $locumSessionsQuery = LocumSession::query();

        if ($request->has('practice')) {
            // Get practice
            $practice = Practice::findOrFail($request->practice);

            $locumSessionsQuery = $locumSessionsQuery->where('practice_id', $practice->id);
        }

        if ($request->has('role')) {
            // Get role
            $role = Role::findOrFail($request->role);

            $locumSessionsQuery = $locumSessionsQuery->where('role_id', $role->id);
        }

        if ($request->has('start_date')) {
            // Start Date
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date);

            $locumSessionsQuery = $locumSessionsQuery->whereDate('start_date', $startDate);
        }

        if ($request->has('end_date')) {
            // End Date
            $endDate = Carbon::createFromFormat('Y-m-d', $request->end_date);

            $locumSessionsQuery = $locumSessionsQuery->whereDate('end_date', $endDate);
        }

        if ($request->has('rate')) {
            // Parse rate
            $rate = $request->rate;

            $locumSessionsQuery = $locumSessionsQuery->where('rate', $rate);
        }

        if ($request->has('name')) {
            $name = $request->name;

            $locumSessionsQuery = $locumSessionsQuery->where('name', 'like', '%' . $name . '%');
        }

        if ($request->has('quantity')) {
            $quantity = $request->quantity;

            $locumSessionsQuery = $locumSessionsQuery->where('quantity', $quantity);

        }

        if ($request->has('unit')) {
            $unit = $request->unit;

            $$locumSessionsQuery = $locumSessionsQuery->where('unit', $unit);
        }

        $filteredLocumSessions = $locumSessionsQuery->whereHas('locums', function ($q) use ($authenticatedUser) {
            $q->where('user_id', $authenticatedUser->id);
        })
            ->with('practice', 'role', 'locums.profile', 'locums.roles', 'locumNotes', 'qualifications')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'locum-sessions' => $filteredLocumSessions,
        ]);
    }

    // Fetch sessions by month
    public function fetchUserSessionsByMonth($request)
    {
        // Get authenticated user
        $authenticatedUser = auth()->user();

        // Cast $request->date to variable
        $date = $request->date;

        // Parsing $date with Carbon
        $parsedDate = Carbon::createFromFormat('Y-m', $date);

        // Build session by month query
        $sessionsByMonthQuery = LocumSession::query();

        // Check if $request has location
        if ($request->has('location')) {
            $location = Practice::findOrFail($request->location);

            $sessionsByMonthQuery = $sessionsByMonthQuery->where('practice_id', $location->id);
        }

        // Get session by month
        $sessionsByMonthFiltered = $sessionsByMonthQuery->whereHas('locums', function ($q) use ($authenticatedUser) {
            $q->where('user_id', $authenticatedUser->id);
        })
            ->whereMonth('start_date', '=', $parsedDate->format('m'))
            ->with(['locums.profile', 'locums.roles', 'locums.locumNotes', 'locums.qualifications'])
            ->withCount(['locums'])
            ->latest()
            ->get();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'sessions-by-month' => $sessionsByMonthFiltered,
        ]);

    }

    // Fetch sessions by day
    public function fetchUserSessionsByDay($request)
    {

        // Authenticated user
        $authenticatedUser = auth()->user();

        // Cast $request->date to variable
        $date = $request->date;

        // Parsing $date with Carbon
        $parsedDate = Carbon::createFromFormat('Y-m-d', $date);

        // Get sessions by the date
        $sessionsByDay = LocumSession::whereHas('locums', function ($q) use ($authenticatedUser) {
            $q->where('user_id', $authenticatedUser->id);
        })
            ->whereDate('start_date', '=', $parsedDate->format('Y-m-d'))
            ->with(['locums.profile', 'locums.roles', 'locums.locumNotes', 'locums.qualifications'])
            ->withCount(['locums'])
            ->latest()
            ->get();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'sessions-by-day' => $sessionsByDay,
        ]);

    }

    // Fetch user's locum session invites
    public function getMySessionInvites($request)
    {
        // Get authenticated
        $authenticatedUser = auth()->user();

        // Cast $request->date to variable
        $date = $request->date;

        // Parsing $date with Carbon
        $parsedDate = Carbon::createFromFormat('Y-m', $date);

        // Get $authenticatedUser invites
        $invites = LocumSessionInvite::whereHas('session', function ($q) use ($parsedDate) {
            $q->whereMonth('start_date', '=', $parsedDate->format('m'));
        })
            ->where('locum', $authenticatedUser->id)
            ->with(['session'])
            ->latest()
            ->get();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'session-invites' => $invites,
        ]);
    }

    // Fetch hired users
    public function fetchHiredWithNoInductionUsers($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get hired users for the practice
        $hiredUsers = User::whereHas('practices', function ($q) use ($practice) {
            $q->where('practice_id', $practice->id);
        })
            ->whereDoesntHave('inductionSchedule')
            ->where([
                'is_hired' => Config::get('constants.USER.HIRED'),
            ])->with([
            'profile.applicant',
            'positionSummary',
            'contractSummary',
            'roles',
            'practices',
            'employmentCheck',
            'workPatterns.workTimings',
            'courses.modules.lessons',
            'locumNotes',
            'qualifications',
            'locumSessions' => function ($q) {
                $q->latest();
            },
            'interviewSchedules.interviewMiscInfo',
            'interviewSchedules.interviewScore',
            'offers' => function ($q) {
                $q->orderBy('id', 'desc')->limit(1);
            },
            'offers.amendments',
            'inductionSchedule',
        ])
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'hired-users' => $hiredUsers,
        ]);
    }

    // Assign courses to candidate when hired
    private function assignCoursesToCandidateWhenHired($roleID, $candidate)
    {
        // Get courses by $roleID
        $coursesByRole = TrainingCourse::whereHas('roles', function ($q) use ($roleID) {
            $q->where('role_id', $roleID);
        })->get();

        // Loop through $coursesByRole and attach with $candidate
        foreach ($coursesByRole as $courseByRole):
            // Start date
            $startDate = Carbon::now();

            $candidate->courses()->attach($courseByRole->id, [
                'start_date' => $startDate->format('Y-m-d'),
                'due_date' => $startDate->addMonths(3)->format('Y-m-d'),
                'inProgress' => Config::get('constants.TRAINING_COURSE.IN_PROGRESS'),
            ]);
        endforeach;
    }

    // Fetch user's policies
    public function fetchUserPolicies()
    {
        // Get authenticated user
        $authenticatedUser = auth()->user();

        $user = User::findOrFail($authenticatedUser->id);

        $userRole = $user->roles[0]->id;

        // Get policies for user attached via role
        $policies = Policy::whereHas('roles', function ($q) use ($userRole) {
            $q->where('role_id', $userRole);
        })
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'policies' => $policies,
        ]);
    }

    // Update course in progress status in training_course_user pivot table
    private function updateInProgressStatusForCourse($user, $course)
    {
        $user->courses()->updateExistingPivot($course->id, ['in_progress' => 0]);
    }
}