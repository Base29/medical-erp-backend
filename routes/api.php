<?php

use App\Http\Controllers\Answer\AnswerController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CheckList\CheckListController;
use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\ContractSummary\ContractSummaryController;
use App\Http\Controllers\Department\DepartmentController;
use App\Http\Controllers\Education\EducationController;
use App\Http\Controllers\EmergencyContact\EmergencyContactController;
use App\Http\Controllers\EmploymentCheck\EmploymentCheckController;
use App\Http\Controllers\EmploymentHistory\EmploymentHistoryController;
use App\Http\Controllers\EmploymentPolicy\EmploymentPolicyController;
use App\Http\Controllers\HeadQuarter\HeadQuarterController;
use App\Http\Controllers\HiringRequest\HiringRequestController;
use App\Http\Controllers\InductionChecklist\InductionChecklistController;
use App\Http\Controllers\InductionResult\InductionResultController;
use App\Http\Controllers\InductionSchedule\InductionScheduleController;
use App\Http\Controllers\Interview\InterviewController;
use App\Http\Controllers\JobSpecification\JobSpecificationController;
use App\Http\Controllers\Legal\LegalController;
use App\Http\Controllers\MiscellaneousInformation\MiscellaneousInformationController;
use App\Http\Controllers\Permission\PermissionController;
use App\Http\Controllers\PersonSpecification\PersonSpecificationController;
use App\Http\Controllers\Policy\PolicyController;
use App\Http\Controllers\PositionSummary\PositionSummaryController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Practice\PracticeController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Reason\ReasonController;
use App\Http\Controllers\Reference\ReferenceController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\Room\RoomController;
use App\Http\Controllers\Signature\SignatureController;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\Termination\TerminationController;
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

            Route::get('/', [UserController::class, 'fetch']);

            Route::post('update', [UserController::class, 'update']);
        });

    // Endpoint for fetching individual user profile
    Route::post('users/me', [UserController::class, 'me'])->middleware(['permission:can_view_own_profile']);

    // Endpoints for practice operations
    Route::prefix('practices')->group(function () {
        Route::get('/', [PracticeController::class, 'fetch'])
            ->middleware(['permission:can_view_practices']);

        Route::post('create', [PracticeController::class, 'create'])
            ->middleware(['permission:can_create_practice']);

        Route::delete('delete/{id}', [PracticeController::class, 'delete'])
            ->middleware(['permission:can_delete_practice']);

        Route::post('assign-to-user', [PracticeController::class, 'assignToUser'])
            ->middleware(['permission:can_assign_practice']);

        Route::post('revoke-for-user', [PracticeController::class, 'revokeForUser'])
            ->middleware(['permission:can_revoke_practice']);
    });

    // Endpoints for policies
    Route::prefix('policies')->group(function () {
        Route::post('/create', [PolicyController::class, 'create'])
            ->middleware(['permission:can_create_policy']);

        Route::delete('/delete/{id}', [PolicyController::class, 'delete'])
            ->middleware(['permission:can_delete-policy']);

        Route::get('/', [PolicyController::class, 'fetch'])
            ->middleware(['permission:can_view_policies']);

        Route::post('sign-policy', [SignatureController::class, 'signPolicy'])
            ->middleware(['permission:can_sign_policy']);
    });

    // Endpoints for room operations
    Route::prefix('rooms')->group(function () {
        Route::post('/', [RoomController::class, 'fetch'])
            ->middleware(['permission:can_view_rooms']);

        Route::post('create', [RoomController::class, 'create'])
            ->middleware(['permission:can_create_room']);

        Route::delete('delete/{id}', [RoomController::class, 'delete'])
            ->middleware(['permission:can_delete_room']);

        Route::post('update', [RoomController::class, 'update'])
            ->middleware(['permission:can_update_room']);
    });

    Route::prefix('reasons')->group(function () {
        Route::get('/', [ReasonController::class, 'fetch'])
            ->middleware(['permission:can_view_reasons']);

        Route::post('create', [ReasonController::class, 'create'])
            ->middleware(['permission:can_create_reason']);

        Route::delete('delete/{id}', [ReasonController::class, 'delete'])
            ->middleware(['permission:can_delete_reason']);
    });

    // Endpoints for CheckList Operations
    Route::prefix('checklists')->group(function () {
        Route::post('/', [CheckListController::class, 'fetch'])
            ->middleware(['permission:can_view_checklists']);

        Route::post('create', [CheckListController::class, 'create'])
            ->middleware(['permission:can_create_checklist']);
    });

    // Endpoints for Task operations
    Route::prefix('tasks')->group(function () {
        Route::post('update', [TaskController::class, 'update'])
            ->middleware(['permission:can_update_task']);

        Route::post('create', [TaskController::class, 'create'])
            ->middleware(['permission:can_create_task']);

        Route::delete('delete/{id}', [TaskController::class, 'delete'])
            ->middleware(['permission:can_delete_task']);
    });

    // Routes for cleaner forum (Communication Book)
    Route::prefix('communication-book')->group(function () {
        Route::get('/', [PostController::class, 'fetch'])
            ->middleware(['permission:can_fetch_posts|can_fetch_communication_book_posts']);

        Route::post('me', [PostController::class, 'me'])
            ->middleware(['permission:can_fetch_own_posts']);

        Route::post('create', [PostController::class, 'create'])
            ->middleware(['permission:can_create_post']);

        Route::delete('delete/{id}', [PostController::class, 'delete'])
            ->middleware(['permission:can_delete_own_post']);

        Route::post('update', [PostController::class, 'update'])
            ->middleware(['permission:can_update_post']);

        Route::post('post', [PostController::class, 'fetchSinglePost'])
            ->middleware(['permission:can_view_post']);

        Route::post('post-view', [PostController::class, 'postView']);

        // Routes for answer
        Route::prefix('answers')->group(function () {
            Route::post('create', [AnswerController::class, 'create'])
                ->middleware(['permission:can_create_answer']);

            Route::post('/', [AnswerController::class, 'fetch']);

            Route::post('update', [AnswerController::class, 'update'])
                ->middleware(['permission:can_update_answer']);

            Route::delete('delete/{id}', [AnswerController::class, 'delete'])
                ->middleware(['permission:can_delete_answer']);
        });

        // Routes for comments
        Route::prefix('comments')->group(function () {
            Route::post('create', [CommentController::class, 'create'])
                ->middleware(['permission:can_create_comment']);

            Route::post('update', [CommentController::class, 'update'])
                ->middleware(['permission:can_update_comment']);

            Route::delete('delete/{id}', [CommentController::class, 'delete'])
                ->middleware(['permission:can_delete_comment']);
        });
    });

    // Routes for signatures
    Route::prefix('signatures')->group(function () {
        Route::get('/', [SignatureController::class, 'fetch'])
            ->middleware(['permission:can_fetch_signatures']);
    });

    // Routes for contract summary
    Route::prefix('contract-summaries')->group(function () {
        Route::post('create', [ContractSummaryController::class, 'create'])
            ->middleware(['permission:can_create_contract_summary']);

        Route::post('update', [ContractSummaryController::class, 'update'])
            ->middleware(['permission:can_update_contract_summary']);

        Route::post('contract-summary', [ContractSummaryController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_single_contract_summary']);

        Route::delete('delete/{id}', [ContractSummaryController::class, 'delete'])
            ->middleware(['permission:can_delete_contract_summary']);
    });

    // Routes for position summary
    Route::prefix('position-summaries')->group(function () {
        Route::post('create', [PositionSummaryController::class, 'create'])
            ->middleware(['permission:can_create_position_summary']);

        Route::post('update', [PositionSummaryController::class, 'update'])
            ->middleware(['permission:can_update_position_summary']);

        Route::post('position-summary', [PositionSummaryController::class, 'fetchSingle'])->middleware(['permission:can_fetch_single_position_summary']);

        Route::delete('delete/{id}', [PositionSummaryController::class, 'delete'])
            ->middleware(['permission:can_delete_position_summary']);
    });

    // Routes for work pattern
    Route::prefix('work-patterns')->group(function () {
        Route::post('create', [WorkPatternController::class, 'create'])
            ->middleware(['permission:can_create_work_pattern']);

        Route::get('/', [WorkPatternController::class, 'fetch'])
            ->middleware(['permission:can_fetch_work_patterns']);

        Route::delete('delete/{id}', [WorkPatternController::class, 'delete'])
            ->middleware(['permission:can_delete_work_pattern']);
    });

    Route::prefix('work-timings')->group(function () {
        Route::post('update', [WorkTimingController::class, 'update'])
            ->middleware(['permission:can_update_work_timing']);

        Route::post('/', [WorkTimingController::class, 'fetch'])
            ->middleware(['permission:can_fetch_work_patterns']);

    });

    // Routes for profile
    Route::prefix('profiles')->group(function () {

        Route::post('update', [ProfileController::class, 'update'])
            ->middleware(['permission:can_update_profile']);
    });

    Route::prefix('misc-info')->group(function () {
        Route::post('create', [MiscellaneousInformationController::class, 'create'])
            ->middleware(['permission:can_create_misc_info']);

        Route::post('/', [MiscellaneousInformationController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_misc_info']);

        Route::post('delete', [MiscellaneousInformationController::class, 'delete'])
            ->middleware(['permission:can_delete_misc_info']);

        Route::post('update', [MiscellaneousInformationController::class, 'update'])
            ->middleware(['permission:can_update_misc_info']);
    });

    // Routes for Employment Checks
    Route::prefix('employment-checks')->group(function () {
        Route::post('create', [EmploymentCheckController::class, 'create'])
            ->middleware(['permission:can_create_employment_check']);

        Route::post('update', [EmploymentCheckController::class, 'update'])
            ->middleware(['permission:can_update_employment_check']);

        Route::post('delete', [EmploymentCheckController::class, 'delete'])
            ->middleware(['permission:can_delete_employment_check']);

        Route::post('/', [EmploymentCheckController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_single_employment_check']);
    });

    // Routes for Employment Policies
    Route::prefix('employment-policies')->group(function () {
        Route::post('create', [EmploymentPolicyController::class, 'create'])
            ->middleware(['permission:can_create_employment_policy']);

        Route::post('update', [EmploymentPolicyController::class, 'update'])
            ->middleware(['permission:can_update_employment_policy']);

        Route::post('delete', [EmploymentPolicyController::class, 'delete'])
            ->middleware(['permission:can_delete_employment_policy']);

        Route::post('/', [EmploymentPolicyController::class, 'fetch'])
            ->middleware(['permission:can_fetch_employment_policies']);
    });

    // Routes for employment history
    Route::prefix('employment-histories')->group(function () {
        Route::post('create', [EmploymentHistoryController::class, 'create'])
            ->middleware(['permission:can_create_employment_history']);

        Route::post('update', [EmploymentHistoryController::class, 'update'])
            ->middleware(['permission:can_update_employment_history']);

        Route::post('delete', [EmploymentHistoryController::class, 'delete'])
            ->middleware(['permission:can_delete_employment_history']);

        Route::post('/', [EmploymentHistoryController::class, 'fetch'])
            ->middleware(['permission:can_fetch_employment_history']);

        Route::post('employment-history', [EmploymentHistoryController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_single_employment_history']);
    });

    // Routes for references
    Route::prefix('references')->group(function () {
        Route::post('create', [ReferenceController::class, 'create'])
            ->middleware(['permission:can_create_reference']);

        Route::post('/', [ReferenceController::class, 'fetch'])
            ->middleware(['permission:can_fetch_user_references']);

        Route::post('delete', [ReferenceController::class, 'delete'])
            ->middleware(['permission:can_delete_reference']);

        Route::post('update', [ReferenceController::class, 'update'])
            ->middleware(['permission:can_update_reference']);
    });

    // Routes for education
    Route::prefix('education')->group(function () {
        Route::post('create', [EducationController::class, 'create'])
            ->middleware(['permission:can_create_education']);

        Route::post('/', [EducationController::class, 'fetch'])
            ->middleware(['permission:can_fetch_education']);

        Route::post('delete', [EducationController::class, 'delete'])
            ->middleware(['permission:can_delete_education']);

        Route::post('update', [EducationController::class, 'update'])
            ->middleware(['permission:can_update_education']);
    });

    // Routes for legal
    Route::prefix('legals')->group(function () {
        Route::post('/', [LegalController::class, 'fetch'])
            ->middleware(['permission:can_fetch_user_legal']);

        Route::post('create', [LegalController::class, 'create'])
            ->middleware(['permission:can_create_legal']);

        Route::post('delete', [LegalController::class, 'delete'])
            ->middleware(['permission:can_delete_legal']);

        Route::post('update', [LegalController::class, 'update'])
            ->middleware(['permission:can_update_legal']);
    });

    // Routes for emergency contact
    Route::prefix('emergency-contacts')->group(function () {
        Route::post('create', [EmergencyContactController::class, 'create'])
            ->middleware(['permission:can_create_emergency_contact']);

        Route::post('/', [EmergencyContactController::class, 'fetch'])
            ->middleware(['permission:can_fetch_emergency_contact']);

        Route::post('update', [EmergencyContactController::class, 'update'])
            ->middleware(['permission:can_update_emergency_contact']);

        Route::post('delete', [EmergencyContactController::class, 'delete'])
            ->middleware(['permission:can_delete_emergency_contact']);
    });

    // Routes for termination
    Route::prefix('terminations')->group(function () {
        Route::post('create', [TerminationController::class, 'create'])
            ->middleware(['permission:can_create_termination']);

        Route::post('/', [TerminationController::class, 'fetch'])
            ->middleware(['permission:can_fetch_termination']);

        Route::post('update', [TerminationController::class, 'update'])
            ->middleware(['permission:can_update_termination']);

        Route::post('delete', [TerminationController::class, 'delete'])
            ->middleware(['permission:can_delete_termination']);
    });

    // Routes for hiring request
    Route::prefix('hiring-requests')->group(function () {
        Route::post('create', [HiringRequestController::class, 'create'])
            ->middleware(['permission:can_create_hiring_request']);

        Route::post('hiring-request', [HiringRequestController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_single_hiring_request']);

        Route::post('update', [HiringRequestController::class, 'update'])
            ->middleware(['permission:can_update_hiring_request']);

        Route::post('delete', [HiringRequestController::class, 'delete'])
            ->middleware(['permission:can_delete_hiring_request']);

        Route::post('/', [HiringRequestController::class, 'fetch'])
            ->middleware(['permission:can_fetch_hiring_request']);
    });

    // Routes for induction checklist
    Route::prefix('induction-checklists')->group(function () {
        Route::post('create', [InductionChecklistController::class, 'create'])
            ->middleware(['permission:can_create_induction_checklist']);

        Route::post('/', [InductionChecklistController::class, 'fetch'])
            ->middleware(['permission:can_fetch_induction_checklists']);

        Route::post('induction-checklist', [InductionChecklistController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_single_induction_checklist']);

        Route::post('delete', [InductionChecklistController::class, 'delete'])
            ->middleware(['permission:can_delete_induction_checklist']);

        Route::post('update', [InductionChecklistController::class, 'update'])
            ->middleware(['permission:can_update_induction_checklist']);
    });

    // Routes for induction schedules
    Route::prefix('induction-schedules')->group(function () {
        Route::post('create', [InductionScheduleController::class, 'create'])
            ->middleware(['permission:can_create_induction_schedule']);

        Route::post('/', [InductionScheduleController::class, 'fetch'])
            ->middleware(['permission:can_fetch_practice_induction_schedules']);
    });

    // Routes for induction results
    Route::prefix('induction-results')->group(function () {
        Route::post('create', [InductionResultController::class, 'create'])
            ->middleware(['permission:can_create_induction_result']);
    });

    // Routes for departments
    Route::prefix('departments')->group(function () {
        Route::post('create', [DepartmentController::class, 'create'])
            ->middleware(['permission:can_create_department']);

        Route::post('/', [DepartmentController::class, 'fetch'])
            ->middleware(['permission:can_fetch_department']);

        Route::post('delete', [DepartmentController::class, 'delete'])
            ->middleware(['permission:can_delete_department']);
    });

    // Routes for job specifications
    Route::prefix('job-specifications')->group(function () {
        Route::post('create', [JobSpecificationController::class, 'create'])
            ->middleware(['permission:can_create_job_specification']);

        Route::post('/', [JobSpecificationController::class, 'fetch'])
            ->middleware(['permission:can_fetch_job_specification']);

        Route::post('delete', [JobSpecificationController::class, 'delete'])
            ->middleware(['permission:can_delete_job_specification']);

        Route::post('job-specification', [JobSpecificationController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_single_job_specification']);
    });

    // Routes for person specifications
    Route::prefix('person-specifications')->group(function () {
        Route::post('create', [PersonSpecificationController::class, 'create'])
            ->middleware(['permission:can_create_person_specification']);

        Route::post('/', [PersonSpecificationController::class, 'fetch'])
            ->middleware(['permission:can_fetch_person_specification']);

        Route::post('delete', [PersonSpecificationController::class, 'delete'])
            ->middleware(['permission:can_delete_person_specification']);

        Route::post('person-specification', [PersonSpecificationController::class, 'fetchSingle'])
            ->middleware(['permission:can_fetch_single_person_specification']);
    });

    // Routes for HQ
    Route::prefix('hq')->group(function () {
        Route::post('process-hiring-request', [HeadQuarterController::class, 'processHiringRequest'])
            ->middleware(['permission:can_process_hiring_request']);

        Route::post('offers', [HeadQuarterController::class, 'fetchOffers'])
            ->middleware(['permission:can_fetch_offers']);

        Route::post('hiring-requests', [HiringRequestController::class, 'fetch'])
            ->middleware(['permission:can_fetch_hiring_request']);
    });

    // Routes for interviews
    Route::prefix('interviews')->group(function () {
        Route::post('/', [InterviewController::class, 'fetch'])
            ->middleware(['permission:can_fetch_interviews']);

        Route::post('schedules', [InterviewController::class, 'interviewSchedules'])
            ->middleware(['permission:can_fetch_interview_schedules']);
    });
});