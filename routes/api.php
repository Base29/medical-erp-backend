<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CheckList\CheckListController;
use App\Http\Controllers\Permission\PermissionController;
use App\Http\Controllers\Policy\PolicyController;
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
    Route::post('forgot-password', [AuthController::class, 'generate_reset_password_link'])->name('forgot.password');
    Route::post('reset-password', [AuthController::class, 'reset_password'])->name('reset.password');
    Route::post('verify-token', [AuthController::class, 'verify_token']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware(['auth:api'])->name('logout');
});

Route::middleware(['auth:api'])->group(function () {
    // Endpoints accessible by super_admin only
    Route::middleware(['role:super_admin'])->group(function () {
        // Endpoints for role operations
        Route::prefix('roles')->group(function () {
            Route::get('/', [RoleController::class, 'fetch']);
            Route::post('create', [RoleController::class, 'create']);
            Route::post('assign', [RoleController::class, 'assign_to_user']);
            Route::post('revoke', [RoleController::class, 'revoke_for_user']);
            Route::delete('delete/{id}', [RoleController::class, 'delete']);
        });

        // Endpoints for permissions operations
        Route::prefix('permissions')->group(function () {
            Route::get('/', [PermissionController::class, 'fetch']);
            Route::post('create', [PermissionController::class, 'create']);
            Route::delete('delete/{id}', [PermissionController::class, 'delete']);
            Route::post('assign-to-user', [PermissionController::class, 'assign_to_user']);
            Route::post('assign-to-role', [PermissionController::class, 'assign_to_role']);
            Route::post('revoke-for-user', [PermissionController::class, 'revoke_for_user']);
            Route::post('revoke-for-role', [PermissionController::class, 'revoke_for_role']);
        });

        // Endpoints for practice operations
        Route::prefix('practices')->group(function () {
            Route::get('/', [PracticeController::class, 'fetch']);
            Route::post('create', [PracticeController::class, 'create']);
            Route::delete('delete/{id}', [PracticeController::class, 'delete']);
            Route::post('assign-to-user', [PracticeController::class, 'assign_to_user']);
            Route::post('revoke-for-user', [PracticeController::class, 'revoke_for_user']);
        });

        // Endpoints for policies
        Route::prefix('policies')->group(function () {
            Route::post('/create', [PolicyController::class, 'create']);
            Route::delete('/delete/{id}', [PolicyController::class, 'delete']);
        });

        // Endpoints for user operations
        Route::prefix('users')->group(function () {
            Route::post('create', [UserController::class, 'create']);
            Route::delete('delete/{id}', [UserController::class, 'delete']);
            Route::get('/', [UserController::class, 'fetch']);
        });
    });

    // Routes accessible through permissions
    Route::post('sign-policy', SignPolicyController::class);
    Route::post('rooms/', [RoomController::class, 'fetch'])->middleware(['permission:view_rooms']);
    Route::get('policies/', [PolicyController::class, 'fetch'])->middleware(['permission:view_policies'])->name('policies');
    Route::get('reasons/', [ReasonController::class, 'fetch'])->middleware(['permission:view_reasons']);
    Route::post('checklists/', [CheckListController::class, 'fetch'])->middleware(['permission:view_checklists']);
    Route::post('tasks/update', [TaskController::class, 'update'])->middleware(['permission:can_update_task']);

    // Routes accessible by super admin and managers only
    Route::middleware(['role:manager|super_admin'])->group(function () {
        // Endpoints for room operations
        Route::prefix('rooms')->group(function () {
            // Route::post('/', [RoomController::class, 'fetch']);
            Route::post('create', [RoomController::class, 'create']);
            Route::delete('delete/{id}', [RoomController::class, 'delete']);
            Route::post('update', [RoomController::class, 'update']);
        });

        Route::prefix('reasons')->group(function () {
            Route::post('create', [ReasonController::class, 'create']);
            Route::delete('delete/{id}', [ReasonController::class, 'delete']);
        });

        // Endpoints for CheckList Operations
        Route::prefix('checklists')->group(function () {
            Route::post('create', [CheckListController::class, 'create']);
        });

        // Endpoints for Task operations
        Route::prefix('tasks')->group(function () {
            Route::post('create', [TaskController::class, 'create']);
            Route::delete('delete/{id}', [TaskController::class, 'delete']);
        });
    });

});