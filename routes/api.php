<?php

use App\Http\Controllers\Answer\AnswerController;
use App\Http\Controllers\AppraisalPolicy\AppraisalPolicyController;
use App\Http\Controllers\Appraisal\AppraisalController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CheckList\CheckListController;
use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\ContractSummary\ContractSummaryController;
use App\Http\Controllers\Department\DepartmentController;
use App\Http\Controllers\Education\EducationController;
use App\Http\Controllers\EmergencyContact\EmergencyContactController;
use App\Http\Controllers\EmployeeHandbook\EmployeeHandbookController;
use App\Http\Controllers\EmploymentCheck\EmploymentCheckController;
use App\Http\Controllers\EmploymentHistory\EmploymentHistoryController;
use App\Http\Controllers\EmploymentPolicy\EmploymentPolicyController;
use App\Http\Controllers\HeadQuarter\HeadQuarterController;
use App\Http\Controllers\HiringRequest\HiringRequestController;
use App\Http\Controllers\InductionChecklist\InductionChecklistController;
use App\Http\Controllers\InductionResult\InductionResultController;
use App\Http\Controllers\InductionSchedule\InductionScheduleController;
use App\Http\Controllers\InterviewPolicy\InterviewPolicyController;
use App\Http\Controllers\Interview\InterviewController;
use App\Http\Controllers\ItPolicy\ItPolicyController;
use App\Http\Controllers\JobSpecification\JobSpecificationController;
use App\Http\Controllers\Legal\LegalController;
use App\Http\Controllers\Locum\LocumController;
use App\Http\Controllers\MiscellaneousInformation\MiscellaneousInformationController;
use App\Http\Controllers\Offer\OfferController;
use App\Http\Controllers\Permission\PermissionController;
use App\Http\Controllers\PersonSpecification\PersonSpecificationController;
use App\Http\Controllers\Policy\PolicyController;
use App\Http\Controllers\PositionSummary\PositionSummaryController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Practice\PracticeController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Qualification\QualificationController;
use App\Http\Controllers\Reason\ReasonController;
use App\Http\Controllers\Reference\ReferenceController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\Room\RoomController;
use App\Http\Controllers\Signature\SignatureController;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\Termination\TerminationController;
use App\Http\Controllers\TrainingCourse\TrainingCourseController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\WorkPattern\WorkPatternController;
use App\Http\Controllers\WorkTiming\WorkTimingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
//TODO: Add prefixes for the all of the API endpoints

// Routes for authentication and password reset
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');

    Route::post('forgot-password', [AuthController::class, 'generateResetPasswordLink'])
        ->name('forgot.password');

    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->name('reset.password');

    Route::post('verify-token', [AuthController::class, 'verifyToken']);

    Route::post('logout', [AuthController::class, 'logout'])
        ->middleware(['auth:api'])->name('logout');
});

Route::middleware(['auth:api'])->group(function () {

    // Endpoints for role operations
    Route::prefix('roles')
        ->middleware(['permission:can_manage_role'])
        ->group(function () {
            Route::get('/', [RoleController::class, 'fetch']);

            Route::post('create', [RoleController::class, 'create']);

            Route::post('assign', [RoleController::class, 'assignToUser']);

            Route::post('revoke', [RoleController::class, 'revokeForUser']);

            Route::delete('delete/{id}', [RoleController::class, 'delete']);
        });

    // Endpoints for permissions operations
    Route::prefix('permissions')
        ->middleware(['permission:can_manage_permission'])
        ->group(function () {
            Route::get('/', [PermissionController::class, 'fetch']);

            Route::post('create', [PermissionController::class, 'create']);

            Route::delete('delete/{id}', [PermissionController::class, 'delete']);

            Route::post('assign-to-user', [PermissionController::class, 'assignToUser']);

            Route::post('assign-to-role', [PermissionController::class, 'assignToRole']);

            Route::post('revoke-for-user', [PermissionController::class, 'revokeForUser']);

            Route::post('revoke-for-role', [PermissionController::class, 'revokeForRole']);
        });

    // Endpoints for user operations
    Route::prefix('users')
        ->middleware(['permission:can_manage_user'])
        ->group(function () {
            Route::post('create', [UserController::class, 'create']);

            Route::delete('delete/{id}', [UserController::class, 'delete']);

            Route::post('/', [UserController::class, 'fetch']);

            Route::patch('update', [UserController::class, 'update']);

            Route::post('user', [UserController::class, 'fetchSingle']);

            Route::post('filter', [UserController::class, 'filter']);

            Route::prefix('qualifications')->group(function () {
                Route::post('create', [QualificationController::class, 'create']);

                Route::patch('update', [QualificationController::class, 'update']);

                Route::post('delete', [QualificationController::class, 'delete']);
            });
        });

    // Endpoints for practice operations
    Route::prefix('practices')->group(function () {
        Route::get('/', [PracticeController::class, 'fetch'])
            ->middleware(['permission:can_view_practices|can_manage_practices']);

        Route::post('create', [PracticeController::class, 'create'])
            ->middleware(['permission:can_create_practice|can_manage_practices']);

        Route::delete('delete/{id}', [PracticeController::class, 'delete'])
            ->middleware(['permission:can_delete_practice|can_manage_practices']);

        Route::post('assign-to-user', [PracticeController::class, 'assignToUser'])
            ->middleware(['permission:can_assign_practice|can_manage_practices']);

        Route::post('revoke-for-user', [PracticeController::class, 'revokeForUser'])
            ->middleware(['permission:can_revoke_practice|can_manage_practices']);
    });

    // Endpoints for policies
    Route::prefix('policies')->group(function () {
        Route::post('/create', [PolicyController::class, 'create'])
            ->middleware(['permission:can_create_policy|can_manage_policies']);

        Route::delete('/delete/{id}', [PolicyController::class, 'delete'])
            ->middleware(['permission:can_delete-policy|can_manage_policies']);

        Route::get('/', [PolicyController::class, 'fetch'])
            ->middleware(['permission:can_view_policies|can_manage_policies']);

        Route::post('sign-policy', [SignatureController::class, 'signPolicy'])
            ->middleware(['permission:can_sign_policy|can_manage_policies']);
    });

    // Endpoints for room operations
    Route::prefix('rooms')->group(function () {
        Route::post('/', [RoomController::class, 'fetch'])
            ->middleware(['permission:can_view_rooms|can_manage_rooms']);

        Route::post('create', [RoomController::class, 'create'])
            ->middleware(['permission:can_create_room|can_manage_rooms']);

        Route::delete('delete/{id}', [RoomController::class, 'delete'])
            ->middleware(['permission:can_delete_room|can_manage_rooms']);

        Route::patch('update', [RoomController::class, 'update'])
            ->middleware(['permission:can_update_room|can_manage_rooms']);
    });

    // Endpoints for reasons
    Route::prefix('reasons')->group(function () {
        Route::get('/', [ReasonController::class, 'fetch'])
            ->middleware(['permission:can_view_reasons|can_manage_reasons']);

        Route::post('create', [ReasonController::class, 'create'])
            ->middleware(['permission:can_create_reason|can_manage_reasons']);

        Route::delete('delete/{id}', [ReasonController::class, 'delete'])
            ->middleware(['permission:can_delete_reason|can_manage_reasons']);
    });

    // Endpoints for CheckList Operations
    Route::prefix('checklists')->group(function () {
        Route::post('/', [CheckListController::class, 'fetch'])
            ->middleware(['permission:can_view_checklists|can_manage_checklists']);

        Route::post('create', [CheckListController::class, 'create'])
            ->middleware(['permission:can_create_checklist|can_manage_checklists']);
    });

    // Endpoints for Task operations
    Route::prefix('tasks')->group(function () {
        Route::patch('update', [TaskController::class, 'update'])
            ->middleware(['permission:can_update_task|can_manage_tasks']);

        Route::post('create', [TaskController::class, 'create'])
            ->middleware(['permission:can_create_task|can_manage_tasks']);

        Route::delete('delete/{id}', [TaskController::class, 'delete'])
            ->middleware(['permission:can_delete_task|can_manage_tasks']);
    });

    // Routes for cleaner forum (Communication Book)
    Route::prefix('communication-book')->group(function () {
        Route::get('/', [PostController::class, 'fetch'])
            ->middleware(['permission:can_fetch_posts|can_fetch_communication_book_posts|can_manage_posts']);

        Route::post('me', [PostController::class, 'me'])
            ->middleware(['permission:can_fetch_own_posts|can_manage_posts']);

        Route::post('create', [PostController::class, 'create'])
            ->middleware(['permission:can_create_post|can_manage_posts']);

        Route::delete('delete/{id}', [PostController::class, 'delete'])
            ->middleware(['permission:can_delete_own_post|can_manage_posts']);

        Route::patch('update', [PostController::class, 'update'])
            ->middleware(['permission:can_update_post|can_manage_posts']);

        Route::post('post', [PostController::class, 'fetchSinglePost'])
            ->middleware(['permission:can_view_post|can_manage_posts']);

        Route::post('post-view', [PostController::class, 'postView']);

        // Routes for answer
        Route::prefix('answers')->group(function () {
            Route::post('create', [AnswerController::class, 'create'])
                ->middleware(['permission:can_create_answer|can_manage_answers']);

            Route::post('/', [AnswerController::class, 'fetch']);

            Route::patch('update', [AnswerController::class, 'update'])
                ->middleware(['permission:can_update_answer|can_manage_answers']);

            Route::delete('delete/{id}', [AnswerController::class, 'delete'])
                ->middleware(['permission:can_delete_answer|can_manage_answers']);
        });

        // Routes for comments
        Route::prefix('comments')->group(function () {
            Route::post('create', [CommentController::class, 'create'])
                ->middleware(['permission:can_create_comment|can_manage_comments']);

            Route::patch('update', [CommentController::class, 'update'])
                ->middleware(['permission:can_update_comment|can_manage_comments']);

            Route::delete('delete/{id}', [CommentController::class, 'delete'])
                ->middleware(['permission:can_delete_comment|can_manage_comments']);
        });
    });

    // Routes for signatures
    Route::prefix('signatures')->group(function () {
        Route::get('/', [SignatureController::class, 'fetch'])
            ->middleware(['permission:can_fetch_signatures|can_manage_signatures']);
    });

    // Routes for contract summary
    Route::prefix('contract-summaries')->group(function () {
        Route::post('create', [ContractSummaryController::class, 'create'])
            ->middleware(['permission:can_create_contract_summary|can_manage_contract_summaries']);

        Route::patch('update', [ContractSummaryController::class, 'update'])
            ->middleware(['permission:can_update_contract_summary|can_manage_contract_summaries']);

        Route::post('contract-summary', [ContractSummaryController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_single_contract_summary|can_manage_contract_summaries']);

        Route::delete('delete/{id}', [ContractSummaryController::class, 'delete'])
            ->middleware(['permission:can_delete_contract_summary|can_manage_contract_summaries']);
    });

    // Routes for position summary
    Route::prefix('position-summaries')->group(function () {
        Route::post('create', [PositionSummaryController::class, 'create'])
            ->middleware(['permission:can_create_position_summary|can_manage_position_summaries']);

        Route::patch('update', [PositionSummaryController::class, 'update'])
            ->middleware(['permission:can_update_position_summary|can_manage_position_summaries']);

        Route::post('position-summary', [PositionSummaryController::class, 'fetchSingle'])->middleware(['permission:can_fetch_single_position_summary|can_manage_position_summaries']);

        Route::delete('delete/{id}', [PositionSummaryController::class, 'delete'])
            ->middleware(['permission:can_delete_position_summary|can_manage_position_summaries']);
    });

    // Routes for work pattern
    Route::prefix('work-patterns')->group(function () {
        Route::post('create', [WorkPatternController::class, 'create'])
            ->middleware(['permission:can_create_work_pattern|can_manage_work_patterns']);

        Route::get('/', [WorkPatternController::class, 'fetch'])
            ->middleware(['permission:can_fetch_work_patterns|can_manage_work_patterns']);

        Route::delete('delete/{id}', [WorkPatternController::class, 'delete'])
            ->middleware(['permission:can_delete_work_pattern|can_manage_work_patterns']);
    });

    Route::prefix('work-timings')->group(function () {
        Route::patch('update', [WorkTimingController::class, 'update'])
            ->middleware(['permission:can_update_work_timing|can_manage_work_patterns']);

        Route::post('/', [WorkTimingController::class, 'fetch'])
            ->middleware(['permission:can_fetch_work_patterns|can_manage_work_patterns']);

    });

    // Routes for profile
    Route::prefix('profiles')->group(function () {

        Route::patch('update', [ProfileController::class, 'update'])
            ->middleware(['permission:can_update_profile|can_manage_profiles']);
    });

    Route::prefix('misc-info')->group(function () {
        Route::post('create', [MiscellaneousInformationController::class, 'create'])
            ->middleware(['permission:can_create_misc_info|can_manage_misc_info']);

        Route::post('/', [MiscellaneousInformationController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_misc_info|can_manage_misc_info']);

        Route::post('delete', [MiscellaneousInformationController::class, 'delete'])
            ->middleware(['permission:can_delete_misc_info|can_manage_misc_info']);

        Route::patch('update', [MiscellaneousInformationController::class, 'update'])
            ->middleware(['permission:can_update_misc_info|can_manage_misc_info']);
    });

    // Routes for Employment Checks
    Route::prefix('employment-checks')->group(function () {
        Route::post('create', [EmploymentCheckController::class, 'create'])
            ->middleware(['permission:can_create_employment_check|can_manage_employment_checks']);

        Route::patch('update', [EmploymentCheckController::class, 'update'])
            ->middleware(['permission:can_update_employment_check|can_manage_employment_checks']);

        Route::post('delete', [EmploymentCheckController::class, 'delete'])
            ->middleware(['permission:can_delete_employment_check|can_manage_employment_checks']);

        Route::post('/', [EmploymentCheckController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_single_employment_check|can_manage_employment_checks']);
    });

    // Routes for Employment Policies
    Route::prefix('employment-policies')->group(function () {
        Route::post('create', [EmploymentPolicyController::class, 'create'])
            ->middleware(['permission:can_create_employment_policy|can_manage_employment_policies']);

        Route::patch('update', [EmploymentPolicyController::class, 'update'])
            ->middleware(['permission:can_update_employment_policy|can_manage_employment_policies']);

        Route::post('delete', [EmploymentPolicyController::class, 'delete'])
            ->middleware(['permission:can_delete_employment_policy|can_manage_employment_policies']);

        Route::post('/', [EmploymentPolicyController::class, 'fetch'])
            ->middleware(['permission:can_fetch_employment_policies|can_manage_employment_policies']);
    });

    // Routes for employment history
    Route::prefix('employment-histories')->group(function () {
        Route::post('create', [EmploymentHistoryController::class, 'create'])
            ->middleware(['permission:can_create_employment_history|can_manage_employment_histories']);

        Route::patch('update', [EmploymentHistoryController::class, 'update'])
            ->middleware(['permission:can_update_employment_history|can_manage_employment_histories']);

        Route::post('delete', [EmploymentHistoryController::class, 'delete'])
            ->middleware(['permission:can_delete_employment_history|can_manage_employment_histories']);

        Route::post('/', [EmploymentHistoryController::class, 'fetch'])
            ->middleware(['permission:can_fetch_employment_history|can_manage_employment_histories']);

        Route::post('employment-history', [EmploymentHistoryController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_single_employment_history|can_manage_employment_histories']);
    });

    // Routes for references
    Route::prefix('references')->group(function () {
        Route::post('create', [ReferenceController::class, 'create'])
            ->middleware(['permission:can_create_reference|can_manage_references']);

        Route::post('/', [ReferenceController::class, 'fetch'])
            ->middleware(['permission:can_fetch_user_references|can_manage_references']);

        Route::post('delete', [ReferenceController::class, 'delete'])
            ->middleware(['permission:can_delete_reference|can_manage_references']);

        Route::patch('update', [ReferenceController::class, 'update'])
            ->middleware(['permission:can_update_reference|can_manage_references']);
    });

    // Routes for education
    Route::prefix('education')->group(function () {
        Route::post('create', [EducationController::class, 'create'])
            ->middleware(['permission:can_create_education|can_manage_education']);

        Route::post('/', [EducationController::class, 'fetch'])
            ->middleware(['permission:can_fetch_education|can_manage_education']);

        Route::post('delete', [EducationController::class, 'delete'])
            ->middleware(['permission:can_delete_education|can_manage_education']);

        Route::patch('update', [EducationController::class, 'update'])
            ->middleware(['permission:can_update_education|can_manage_education']);
    });

    // Routes for legal
    Route::prefix('legals')->group(function () {
        Route::post('/', [LegalController::class, 'fetch'])
            ->middleware(['permission:can_fetch_user_legal|can_manage_legal']);

        Route::post('create', [LegalController::class, 'create'])
            ->middleware(['permission:can_create_legal|can_manage_legal']);

        Route::post('delete', [LegalController::class, 'delete'])
            ->middleware(['permission:can_delete_legal|can_manage_legal']);

        Route::patch('update', [LegalController::class, 'update'])
            ->middleware(['permission:can_update_legal|can_manage_legal']);
    });

    // Routes for emergency contact
    Route::prefix('emergency-contacts')->group(function () {
        Route::post('create', [EmergencyContactController::class, 'create'])
            ->middleware(['permission:can_create_emergency_contact|can_manage_emergency_contacts']);

        Route::post('/', [EmergencyContactController::class, 'fetch'])
            ->middleware(['permission:can_fetch_emergency_contact|can_manage_emergency_contacts']);

        Route::patch('update', [EmergencyContactController::class, 'update'])
            ->middleware(['permission:can_update_emergency_contact|can_manage_emergency_contacts']);

        Route::post('delete', [EmergencyContactController::class, 'delete'])
            ->middleware(['permission:can_delete_emergency_contact|can_manage_emergency_contacts']);
    });

    // Routes for termination
    Route::prefix('terminations')->group(function () {
        Route::post('create', [TerminationController::class, 'create'])
            ->middleware(['permission:can_create_termination|can_manage_terminations']);

        Route::post('/', [TerminationController::class, 'fetch'])
            ->middleware(['permission:can_fetch_termination|can_manage_terminations']);

        Route::patch('update', [TerminationController::class, 'update'])
            ->middleware(['permission:can_update_termination|can_manage_terminations']);

        Route::post('delete', [TerminationController::class, 'delete'])
            ->middleware(['permission:can_delete_termination|can_manage_terminations']);
    });

    // Routes for hiring request
    Route::prefix('hiring-requests')->group(function () {
        Route::post('create', [HiringRequestController::class, 'create'])
            ->middleware(['permission:can_create_hiring_request|can_manage_hiring_requests']);

        Route::post('hiring-request', [HiringRequestController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_single_hiring_request|can_manage_hiring_requests']);

        Route::patch('update', [HiringRequestController::class, 'update'])
            ->middleware(['permission:can_update_hiring_request|can_manage_hiring_requests']);

        Route::post('delete', [HiringRequestController::class, 'delete'])
            ->middleware(['permission:can_delete_hiring_request|can_manage_hiring_requests']);

        Route::post('/', [HiringRequestController::class, 'fetch'])
            ->middleware(['permission:can_fetch_hiring_request|can_manage_hiring_requests']);

        Route::post('add-applicant', [HiringRequestController::class, 'addApplicant'])
            ->middleware(['permission:can_add_applicant|can_manage_hiring_requests']);

        Route::post('applicants', [HiringRequestController::class, 'fetchApplicants'])
            ->middleware(['permission:can_fetch_applicants|can_manage_hiring_requests']);

        Route::prefix('postings')->group(function () {
            Route::post('create', [HiringRequestController::class, 'createPostings'])
                ->middleware(['permission:can_create_posting|can_manage_postings']);

            Route::post('/', [HiringRequestController::class, 'fetchPostings'])
                ->middleware(['permission:can_fetch_postings|can_manage_postings']);

        });
    });

    // Routes for induction checklist
    Route::prefix('induction-checklists')->group(function () {
        Route::post('create', [InductionChecklistController::class, 'create'])
            ->middleware(['permission:can_create_induction_checklist|can_manage_inductions']);

        Route::post('/', [InductionChecklistController::class, 'fetch'])
            ->middleware(['permission:can_fetch_induction_checklists|can_manage_inductions']);

        Route::post('induction-checklist', [InductionChecklistController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_single_induction_checklist|can_manage_inductions']);

        Route::post('delete', [InductionChecklistController::class, 'delete'])
            ->middleware(['permission:can_delete_induction_checklist|can_manage_inductions']);

        Route::patch('update', [InductionChecklistController::class, 'update'])
            ->middleware(['permission:can_update_induction_checklist|can_manage_inductions']);
    });

    // Routes for induction schedules
    Route::prefix('induction-schedules')->group(function () {
        Route::post('create', [InductionScheduleController::class, 'create'])
            ->middleware(['permission:can_create_induction_schedule|can_manage_inductions']);

        Route::post('/', [InductionScheduleController::class, 'fetch'])
            ->middleware(['permission:can_fetch_practice_induction_schedules|can_manage_inductions']);

        Route::post('user-induction', [InductionScheduleController::class, 'userInduction'])
            ->middleware(['permission:can_manage_inductions']);

        Route::post('induction', [InductionScheduleController::class, 'singleInduction'])
            ->middleware(['permission:can_manage_inductions']);

        Route::patch('update', [InductionScheduleController::class, 'update'])
            ->middleware(['permission:can_manage_inductions']);

        Route::post('delete', [InductionScheduleController::class, 'delete'])
            ->middleware(['permission:can_manage_inductions']);
    });

    // Routes for induction results
    Route::prefix('induction-results')->group(function () {
        Route::post('create', [InductionResultController::class, 'create'])
            ->middleware(['permission:can_create_induction_result|can_manage_inductions']);
    });

    // Routes for departments
    Route::prefix('departments')->group(function () {
        Route::post('create', [DepartmentController::class, 'create'])
            ->middleware(['permission:can_create_department|can_manage_departments']);

        Route::post('/', [DepartmentController::class, 'fetch'])
            ->middleware(['permission:can_fetch_department|can_manage_departments']);

        Route::post('delete', [DepartmentController::class, 'delete'])
            ->middleware(['permission:can_delete_department|can_manage_departments']);

        Route::post('assign-user', [DepartmentController::class, 'assignUser'])
            ->middleware(['permission:can_assign_user_to_department|can_manage_departments']);

        Route::post('department', [DepartmentController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_single_department|can_manage_departments']);
    });

    // Routes for job specifications
    Route::prefix('job-specifications')->group(function () {
        Route::post('create', [JobSpecificationController::class, 'create'])
            ->middleware(['permission:can_create_job_specification|can_manage_job_specifications']);

        Route::post('/', [JobSpecificationController::class, 'fetch'])
            ->middleware(['permission:can_fetch_job_specification|can_manage_job_specifications']);

        Route::post('delete', [JobSpecificationController::class, 'delete'])
            ->middleware(['permission:can_delete_job_specification|can_manage_job_specifications']);

        Route::post('job-specification', [JobSpecificationController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_single_job_specification|can_manage_job_specifications']);
    });

    // Routes for person specifications
    Route::prefix('person-specifications')->group(function () {
        Route::post('create', [PersonSpecificationController::class, 'create'])
            ->middleware(['permission:can_create_person_specification|can_manage_person_specifications']);

        Route::post('/', [PersonSpecificationController::class, 'fetch'])
            ->middleware(['permission:can_fetch_person_specification|can_manage_person_specifications']);

        Route::post('delete', [PersonSpecificationController::class, 'delete'])
            ->middleware(['permission:can_delete_person_specification|can_manage_person_specifications']);

        Route::post('person-specification', [PersonSpecificationController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_single_person_specification|can_manage_person_specifications']);
    });

    // Routes for HQ
    Route::prefix('hq')->group(function () {
        Route::prefix('hiring-requests')->group(function () {
            Route::post('/', [HiringRequestController::class, 'fetch'])
                ->middleware(['permission:can_fetch_hiring_request|can_manage_hiring_requests']);

            Route::patch('process-hiring-request', [HeadQuarterController::class, 'processHiringRequest'])
                ->middleware(['permission:can_process_hiring_request|can_manage_hiring_requests']);

            Route::post('search', [HeadQuarterController::class, 'search'])
                ->middleware(['permission:can_search_hiring_requests|can_manage_hiring_requests']);
        });

        Route::prefix('interviews')->group(function () {
            Route::post('up-coming', [InterviewController::class, 'upcomingInterviews'])
                ->middleware(['permission:can_fetch_upcoming_interviews|can_manage_interview']);

            Route::post('/', [InterviewController::class, 'fetch'])
                ->middleware(['permission:can_fetch_all_interviews|can_manage_interview']);

            Route::post('interview', [InterviewController::class, 'singleInterview'])
                ->middleware(['permission:can_manage_interview']);
        });

        // Routes for offer
        Route::prefix('offers')->group(function () {

            Route::post('/', [HeadQuarterController::class, 'fetchOffers'])
                ->middleware(['permission:can_fetch_offers|can_manage_offers']);
        });

    });

    // Routes for Recruiter
    Route::prefix('re')->group(function () {

        // Routes for interviews
        Route::prefix('interviews')->group(function () {
            Route::post('/', [InterviewController::class, 'upcomingInterviews'])
                ->middleware(['permission:can_fetch_interviews|can_manage_interview']);

            Route::patch('update', [InterviewController::class, 'update'])
                ->middleware(['permission:can_update_interview|can_manage_interview']);

            Route::post('delete', [InterviewController::class, 'delete'])
                ->middleware(['permission:can_delete_interview|can_manage_interview']);

            Route::post('create', [InterviewController::class, 'create'])
                ->middleware(['permission:can_create_interview|can_manage_interview']);

            Route::post('past-interviews', [InterviewController::class, 'pastInterviews'])
                ->middleware(['permission:can_fetch_interviews|can_manage_interview']);

            Route::post('answer', [InterviewController::class, 'interviewAnswer'])
                ->middleware(['permission:can_store_interview_answer|can_manage_interview']);

            Route::post('interview', [InterviewController::class, 'singleInterview'])
                ->middleware(['permission:can_fetch_single_interview|can_manage_interview']);

            Route::post('add-misc-info', [InterviewController::class, 'miscInfo'])
                ->middleware(['permission:can_create_interview_misc_info|can_manage_interview']);

            Route::post('create-score', [InterviewController::class, 'score'])
                ->middleware(['permission:can_create_interview_score|can_manage_interview']);

            Route::post('all-interviews', [InterviewController::class, 'getAll'])
                ->middleware(['permission:can_manage_interview']);

            // Routes for interview policies
            Route::prefix('policies')->group(function () {
                Route::post('create', [InterviewPolicyController::class, 'create'])
                    ->middleware(['permission:can_create_interview_policy|can_manage_interview_policy']);

                Route::post('/', [InterviewPolicyController::class, 'fetch'])
                    ->middleware(['permission:can_fetch_interview_policies|can_manage_interview_policy']);

                Route::post('policy', [InterviewPolicyController::class, 'fetchSingle'])
                    ->middleware(['permission:can_fetch_single_interview_policy|can_manage_interview_policy']);

                Route::patch('update', [InterviewPolicyController::class, 'update'])
                    ->middleware(['permission:can_update_interview_policy|can_manage_interview_policy']);

                Route::post('update-question', [InterviewPolicyController::class, 'updateInterviewQuestion'])
                    ->middleware(['permission:can_update_interview_policy_question|can_manage_interview_policy']);

                Route::post('delete', [InterviewPolicyController::class, 'delete'])
                    ->middleware(['permission:can_manage_interview_policy|can_delete_interview_policy']);

                Route::post('role', [InterviewPolicyController::class, 'fetchRolePolicy'])
                    ->middleware(['permission:can_manage_interview_policy']);
            });

            // Routes for Adhoc Questions
            Route::prefix('adhoc-questions')->group(function () {
                Route::post('create', [InterviewController::class, 'adhocQuestions'])
                    ->middleware(['permission:can_create_adhoc_question|can_manage_interview']);

                Route::post('/', [InterviewController::class, 'fetchAdhocQuestions'])
                    ->middleware(['permission:can_fetch_adhoc_questions|can_manage_interview']);

                Route::post('delete', [InterviewController::class, 'deleteAdhocQuestion'])
                    ->middleware(['permission:can_delete_adhoc_question|can_manage_interview']);

            });

            // Routes for candidate questions
            Route::prefix('candidate-questions')->group(function () {
                Route::post('create', [InterviewController::class, 'candidateQuestions'])
                    ->middleware(['permission:can_create_candidate_question|can_manage_interview']);

                Route::post('/', [InterviewController::class, 'fetchCandidateQuestions'])
                    ->middleware(['permission:can_fetch_candidate_questions|can_manage_interview']);

                Route::post('delete', [InterviewController::class, 'deleteCandidateQuestion'])
                    ->middleware(['permission:can_delete_candidate_question']);
            });
        });

        // Route for Locum
        Route::prefix('locums')->group(function () {
            // Route for sessions
            Route::prefix('sessions')->group(function () {
                Route::post('create', [LocumController::class, 'create'])
                    ->middleware(['permission:can_create_locum_session|can_manage_locums']);

                Route::post('add-locum', [LocumController::class, 'assignUser'])
                    ->middleware(['permission:can_assign_user_to_session|can_manage_locums']);

                Route::post('remove-locum', [LocumController::class, 'removeUser'])
                    ->middleware(['permission:can_remove_user_from_session|can_manage_locums']);

                Route::post('/', [LocumController::class, 'fetch'])
                    ->middleware(['permission:can_fetch_locum_sessions|can_manage_locums']);

                Route::post('locum-session', [LocumController::class, 'fetchSingle'])
                    ->middleware(['permission:can_fetch_single_locum_session|can_manage_locums']);

                Route::post('delete', [LocumController::class, 'delete'])
                    ->middleware(['permission:can_delete_locum_session|can_manage_locums']);

                Route::post('month', [LocumController::class, 'fetchByMonth'])
                    ->middleware(['permission:can_manage_locums']);

                Route::post('day', [LocumController::class, 'fetchByDay'])
                    ->middleware(['permission:can_manage_locums']);

                Route::post('invite', [LocumController::class, 'inviteUsersToSession'])
                    ->middleware(['permission:can_manage_locums']);

                Route::post('fetch-user-invites', [UserController::class, 'fetchUserInvites'])
                    ->middleware(['permission:can_manage_locums']);

                Route::prefix('billing')->group(function () {

                    Route::post('/', [LocumController::class, 'fetchLocumInvoices'])
                        ->middleware(['permission:can_manage_locums']);

                    Route::post('update-esm-status', [LocumController::class, 'esmStatus'])
                        ->middleware(['permission:can_manage_locums']);
                });
            });

            Route::patch('user-locum-status', [UserController::class, 'updateLocumStatus'])
                ->middleware(['permission:can_manage_locums']);

            Route::post('add-to-blacklist', [LocumController::class, 'addToBlacklist'])
                ->middleware(['permission:can_manage_locums']);

            Route::post('remove-from-blacklist', [LocumController::class, 'removeFromBlacklist'])
                ->middleware(['permission:can_manage_locums']);

            // Routes for locum notes
            Route::prefix('notes')
                ->middleware(['permission:can_manage_locums'])
                ->group(function () {
                    Route::post('create', [LocumController::class, 'createNote']);

                    Route::patch('update', [LocumController::class, 'updateNote']);

                    Route::post('delete', [LocumController::class, 'deleteNote']);
                });
        });

        // Routes for employee handbook
        Route::prefix('employee-handbooks')->group(function () {
            Route::post('create', [EmployeeHandbookController::class, 'create'])
                ->middleware(['permission:can_manage_employee_handbook|can_create_employee_handbook']);

            Route::get('/', [EmployeeHandbookController::class, 'fetch'])
                ->middleware(['permission:can_manage_employee_handbook|can_fetch_all_employee_handbooks']);

            Route::post('delete', [EmployeeHandbookController::class, 'delete'])
                ->middleware(['permission:can_manage_employee_handbook|can_delete_employee_handbook']);

            Route::post('employee-handbook', [EmployeeHandbookController::class, 'fetchSingle'])
                ->middleware(['permission:can_manage_employee_handbook|can_fetch_single_employee_handbook']);

        });

        // Routes for it policy
        Route::prefix('it-policies')->group(function () {
            Route::post('create', [ItPolicyController::class, 'create'])
                ->middleware(['permission:can_manage_it_policy|can_create_it_policy']);

            Route::get('/', [ItPolicyController::class, 'fetch'])
                ->middleware(['permission:can_manage_it_policy|can_fetch_all_it_policies']);

            Route::post('delete', [ItPolicyController::class, 'delete'])
                ->middleware(['permission:can_manage_it_policy|can_delete_it_policy']);

            Route::post('it-policy', [ItPolicyController::class, 'fetchSingle'])
                ->middleware(['permission:can_manage_it_policy|can_fetch_single_it_policy']);
        });

        // Routes for candidate
        Route::prefix('candidates')->group(function () {
            Route::post('hire', [UserController::class, 'hire'])
                ->middleware('permission:can_manage_candidate');

            Route::post('filter', [UserController::class, 'searchProfiles'])
                ->middleware(['permission:can_manage_candidate']);
        });

        // Routes for vacancies
        Route::prefix('vacancies')->group(function () {
            Route::post('filter', [HiringRequestController::class, 'search'])
                ->middleware(['permission:can_manage_hiring_requests']);
        });

        // Routes for offer
        Route::prefix('offers')->group(function () {
            Route::post('create', [OfferController::class, 'create'])
                ->middleware(['permission:can_create_offer|can_manage_offers']);

            Route::post('/', [HeadQuarterController::class, 'fetchOffers'])
                ->middleware(['permission:can_fetch_offers|can_manage_offers']);

            Route::patch('update', [OfferController::class, 'update'])
                ->middleware(['permission:can_update_offer|can_manage_offers']);

            Route::post('delete', [OfferController::class, 'delete'])
                ->middleware(['permission:can_delete_offer|can_manage_offers']);

            Route::post('offer', [OfferController::class, 'fetchSingle'])
                ->middleware(['permission:can_fetch_single_offer|can_manage_offers']);

            Route::prefix('amendments')->group(function () {
                Route::post('create', [OfferController::class, 'amendOffer'])
                    ->middleware(['permission:can_manage_offer']);

                Route::patch('update', [OfferController::class, 'updateAmendment'])
                    ->middleware(['permission:can_manage_offer']);
            });
        });

    });

    // Routes for US
    Route::prefix('us')->group(function () {
        Route::prefix('me')->middleware(['permission:can_view_own_profile|can_manage_own_profile'])->group(function () {
            Route::post('/', [UserController::class, 'me']);
            Route::post('sign-employee-handbook', [EmployeeHandbookController::class, 'sign']);
            Route::post('sign-it-policies', [ItPolicyController::class, 'sign']);
            Route::post('sign-contract-summary', [ContractSummaryController::class, 'sign']);
            Route::post('update-profile', [ProfileController::class, 'update']);
        });

        // Routes for user's trainings
        Route::prefix('trainings')->middleware(['permission:can_manage_own_trainings'])->group(function () {
            Route::prefix('courses')->group(function () {
                Route::get('/', [UserController::class, 'userTrainingCourses']);
                Route::post('course', [UserController::class, 'singleEnrolledCourse']);
            });

            Route::prefix('progress')->group(function () {
                Route::post('lesson-progress', [UserController::class, 'recordLesson']);
                Route::post('module-progress', [UserController::class, 'recordModule']);
                Route::post('course-progress', [UserController::class, 'recordCourse']);
                Route::post('module-exam', [UserController::class, 'endOfModuleExam']);
            });

        });

        Route::prefix('locum')->middleware(['permission:can_manage_own_locum_sessions'])->group(function () {
            Route::prefix('sessions')->group(function () {
                Route::post('invitation-action', [LocumController::class, 'invitationAction']);
                Route::post('upload-invoice', [LocumController::class, 'uploadInvoice']);
                Route::post('billing', [LocumController::class, 'fetchInvoices']);
                Route::post('/', [UserController::class, 'fetchMyLocumSessions']);
                Route::post('month', [UserController::class, 'fetchMySessionsByMonth']);
                Route::post('day', [UserController::class, 'fetchMySessionsByDay']);
                Route::post('session-invites', [UserController::class, 'fetchMySessionInvites']);
            });
        });
    });

    // Endpoints for Manager
    Route::prefix('ma')->group(function () {
        // Routes for Appraisals
        Route::prefix('appraisals')->group(function () {
            Route::post('/', [AppraisalController::class, 'upcomingAppraisals'])
                ->middleware(['permission:can_manage_appraisal']);

            Route::patch('update', [AppraisalController::class, 'update'])
                ->middleware(['permission:can_manage_appraisal']);

            Route::post('delete', [AppraisalController::class, 'delete'])
                ->middleware(['permission:can_manage_appraisal']);

            Route::post('create', [AppraisalController::class, 'create'])
                ->middleware(['permission:can_manage_appraisal']);

            Route::post('completed-appraisals', [AppraisalController::class, 'completedAppraisals'])
                ->middleware(['permission:can_manage_appraisal']);

            Route::post('answer', [AppraisalController::class, 'appraisalAnswer'])
                ->middleware(['permission:can_manage_appraisal']);

            Route::post('appraisal', [AppraisalController::class, 'singleAppraisal'])
                ->middleware(['permission:can_manage_appraisal']);

            // Routes for Appraisal policies
            Route::prefix('policies')->group(function () {
                Route::post('create', [AppraisalPolicyController::class, 'create'])
                    ->middleware(['permission:can_manage_appraisal_policy']);

                Route::post('/', [AppraisalPolicyController::class, 'fetch'])
                    ->middleware(['permission:can_manage_appraisal_policy']);

                Route::post('policy', [AppraisalPolicyController::class, 'fetchSingle'])
                    ->middleware(['permission:can_manage_appraisal_policy']);

                Route::patch('update', [AppraisalPolicyController::class, 'update'])
                    ->middleware(['permission:can_manage_appraisal_policy']);

                Route::post('update-question', [AppraisalPolicyController::class, 'updateAppraisalQuestion'])
                    ->middleware(['permission:can_manage_appraisal_policy']);

                Route::post('delete', [AppraisalPolicyController::class, 'delete'])
                    ->middleware(['permission:can_manage_appraisal_policy']);

                Route::post('role', [AppraisalPolicyController::class, 'fetchRolePolicy'])
                    ->middleware(['permission:can_manage_appraisal_policy']);
            });

        });

        // Routes for training courses
        Route::prefix('training-courses')->group(function () {
            Route::post('create', [TrainingCourseController::class, 'create'])
                ->middleware(['permission:can_manage_training_course']);

            Route::get('/', [TrainingCourseController::class, 'fetch'])
                ->middleware(['permission:can_manage_training_course']);

            Route::post('training-course', [TrainingCourseController::class, 'singleCourse'])
                ->middleware(['permission:can_manage_training_course']);

            Route::post('delete', [TrainingCourseController::class, 'delete'])
                ->middleware(['permission:can_manage_training_course']);

            Route::patch('update', [TrainingCourseController::class, 'updateCourse'])
                ->middleware(['permission:can_manage_training_course']);

            Route::post('enroll-user', [TrainingCourseController::class, 'enrollCourse'])
                ->middleware(['permission:can_manage_training_course']);

            Route::post('unroll-user', [TrainingCourseController::class, 'unrollCourse'])
                ->middleware(['permission:can_manage_training_course']);

            Route::post('assign-to-users', [TrainingCourseController::class, 'assignToUsers'])
                ->middleware(['permission:can_manage_training_course']);

            Route::post('unassign-users', [TrainingCourseController::class, 'unassignUsers'])
                ->middleware(['permission:can_manage_training_course']);

            Route::prefix('modules')->group(function () {
                Route::post('create', [TrainingCourseController::class, 'createModule'])
                    ->middleware(['permission:can_manage_training_course']);

                Route::prefix('lessons')->group(function () {
                    Route::post('create', [TrainingCourseController::class, 'createLesson'])
                        ->middleware(['permission:can_manage_training_course']);
                });
            });
        });

        Route::prefix('employees')->group(function () {
            Route::get('/', [UserController::class, 'employees'])
                ->middleware(['permission:can_manage_employees']);

            Route::post('hired', [UserController::class, 'fetchHired'])
                ->middleware(['permission:can_manage_employees']);
        });
    });

});