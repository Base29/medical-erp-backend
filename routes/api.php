<?php

use App\Http\Controllers\Answer\AnswerController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CheckList\CheckListController;
use App\Http\Controllers\Comment\CommentController;
use App\Http\Controllers\Permission\PermissionController;
use App\Http\Controllers\Policy\PolicyController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Practice\PracticeController;
use App\Http\Controllers\Reason\ReasonController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\Room\RoomController;
use App\Http\Controllers\Signature\SignPolicyController;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\User\UserController;
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
    Route::post('forgot-password', [AuthController::class, 'generateResetPasswordLink'])->name('forgot.password');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('reset.password');
    Route::post('verify-token', [AuthController::class, 'verifyToken']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware(['auth:api'])->name('logout');
});

Route::middleware(['auth:api'])->group(function () {
    // Endpoints accessible by super_admin only
    Route::middleware(['role:super_admin'])->group(function () {
        // Endpoints for role operations
        Route::prefix('roles')->group(function () {
            Route::get('/', [RoleController::class, 'fetch']);
            Route::post('create', [RoleController::class, 'create']);
            Route::post('assign', [RoleController::class, 'assignToUser']);
            Route::post('revoke', [RoleController::class, 'revokeForUser']);
            Route::delete('delete/{id}', [RoleController::class, 'delete']);
        });

        // Endpoints for permissions operations
        Route::prefix('permissions')->group(function () {
            Route::get('/', [PermissionController::class, 'fetch']);
            Route::post('create', [PermissionController::class, 'create']);
            Route::delete('delete/{id}', [PermissionController::class, 'delete']);
            Route::post('assign-to-user', [PermissionController::class, 'assignToUser']);
            Route::post('assign-to-role', [PermissionController::class, 'assignToRole']);
            Route::post('revoke-for-user', [PermissionController::class, 'revokeForUser']);
            Route::post('revoke-for-role', [PermissionController::class, 'revokeForRole']);
        });

        // Endpoints for practice operations
        Route::prefix('practices')->group(function () {
            Route::get('/', [PracticeController::class, 'fetch']);
            Route::post('create', [PracticeController::class, 'create']);
            Route::delete('delete/{id}', [PracticeController::class, 'delete']);
            Route::post('assign-to-user', [PracticeController::class, 'assignToUser']);
            Route::post('revoke-for-user', [PracticeController::class, 'revokeForUser']);
        });

        // Endpoints for user operations
        Route::prefix('users')->group(function () {
            Route::post('create', [UserController::class, 'create']);
            Route::delete('delete/{id}', [UserController::class, 'delete']);
            Route::get('/', [UserController::class, 'fetch']);
        });
    });

    // Routes accessible through permissions

    // Endpoints for policies
    Route::prefix('policies')->group(function () {
        Route::post('/create', [PolicyController::class, 'create'])->middleware(['permission:can_create_policy']);
        Route::delete('/delete/{id}', [PolicyController::class, 'delete'])->middleware(['permission:can_delete-policy']);
        Route::get('/', [PolicyController::class, 'fetch'])->middleware(['permission:view_policies'])->name('policies');
        Route::post('sign-policy', SignPolicyController::class)->middleware(['permission:can_sign_policy']);
    });

    // Endpoints for room operations
    Route::prefix('rooms')->group(function () {
        Route::post('/', [RoomController::class, 'fetch'])->middleware(['permission:can_view_rooms']);
        Route::post('create', [RoomController::class, 'create'])->middleware(['permission:can_create_room']);
        Route::delete('delete/{id}', [RoomController::class, 'delete'])->middleware(['permission:can_delete_room']);
        Route::post('update', [RoomController::class, 'update'])->middleware(['permission:can_update_room']);
    });

    Route::prefix('reasons')->group(function () {
        Route::get('/', [ReasonController::class, 'fetch'])->middleware(['permission:can_view_reasons']);
        Route::post('create', [ReasonController::class, 'create'])->middleware(['permission:can_create_reason']);
        Route::delete('delete/{id}', [ReasonController::class, 'delete'])->middleware(['permission:can_delete_reason']);
    });

    // Endpoints for CheckList Operations
    Route::prefix('checklists')->group(function () {
        Route::post('/', [CheckListController::class, 'fetch'])->middleware(['permission:can_view_checklists']);
        Route::post('create', [CheckListController::class, 'create'])->middleware(['permission:can_create_checklist']);
    });

    // Endpoints for Task operations
    Route::prefix('tasks')->group(function () {
        Route::post('update', [TaskController::class, 'update'])->middleware(['permission:can_update_task']);
        Route::post('create', [TaskController::class, 'create'])->middleware(['permission:can_create_task']);
        Route::delete('delete/{id}', [TaskController::class, 'delete'])->middleware(['permission:can_delete_task']);
    });

    // Routes for cleaner forum (Communication Book)
    Route::prefix('communication-book')->group(function () {
        Route::get('/', [PostController::class, 'fetch'])->middleware(['permission:can_fetch_posts|can_fetch_communication_book_posts']);
        Route::post('me', [PostController::class, 'me'])->middleware(['permission:can_fetch_own_posts']);
        Route::post('create', [PostController::class, 'create'])->middleware(['permission:can_create_post']);
        Route::delete('delete/{id}', [PostController::class, 'delete'])->middleware(['permission:can_delete_own_post']);
        Route::post('update', [PostController::class, 'update'])->middleware(['permission:can_update_post']);
        Route::post('post', [PostController::class, 'fetchSinglePost'])->middleware(['permission:can_view_post']);
        Route::post('post-view', [PostController::class, 'postView']);

        // Routes for answer
        Route::prefix('answers')->group(function () {
            Route::post('create', [AnswerController::class, 'create'])->middleware(['permission:can_create_answer']);
            Route::post('/', [AnswerController::class, 'fetch']);
            Route::post('update', [AnswerController::class, 'update'])->middleware(['permission:can_update_answer']);
            Route::delete('delete/{id}', [AnswerController::class, 'delete'])->middleware(['permission:can_delete_answer']);
        });

        // Routes for comments
        Route::prefix('comments')->group(function () {
            Route::post('create', [CommentController::class, 'create'])->middleware(['permission:can_create_comment']);
            Route::post('update', [CommentController::class, 'update'])->middleware(['permission:can_update_comment']);
            Route::delete('delete/{id}', [CommentController::class, 'delete'])->middleware(['permission:can_delete_comment']);
        });
    });
});