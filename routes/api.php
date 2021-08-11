<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyTokenController;
use App\Http\Controllers\CheckList\CreateCheckListController;
use App\Http\Controllers\Permission\AssignPermissionToRoleController;
use App\Http\Controllers\Permission\AssignPermissionToUserController;
use App\Http\Controllers\Permission\CreatePermissionController;
use App\Http\Controllers\Permission\DeletePermissionController;
use App\Http\Controllers\Permission\ListPermissionsController;
use App\Http\Controllers\Permission\RevokePermissionForRoleController;
use App\Http\Controllers\Permission\RevokePermissionForUserController;
use App\Http\Controllers\Policy\ListPoliciesController;
use App\Http\Controllers\Practice\AssignPracticeToUserController;
use App\Http\Controllers\Practice\CreatePracticeController;
use App\Http\Controllers\Practice\DeletePracticeController;
use App\Http\Controllers\Practice\ListPracticesController;
use App\Http\Controllers\Practice\RevokePracticeForUserController;
use App\Http\Controllers\Role\AssignRoleController;
use App\Http\Controllers\Role\CreateRoleController;
use App\Http\Controllers\Role\DeleteRoleController;
use App\Http\Controllers\Role\ListRolesController;
use App\Http\Controllers\Role\RevokeRoleController;
use App\Http\Controllers\Room\CreateRoomController;
use App\Http\Controllers\Room\DeleteRoomController;
use App\Http\Controllers\Room\ListRoomsController;
use App\Http\Controllers\Signature\SignPolicyController;
use App\Http\Controllers\User\CreateUserController;
use App\Http\Controllers\User\DeleteUserController;
use App\Http\Controllers\User\ListUsersController;
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
    Route::post('login', LoginController::class)->name('login');
    Route::post('forgot-password', PasswordResetLinkController::class)->name('forgot.password');
    Route::post('reset-password', PasswordResetController::class)->name('reset.password');
    Route::post('verify-token', VerifyTokenController::class);
    Route::post('logout', LogoutController::class)->middleware(['auth:api'])->name('logout');
});

Route::middleware(['auth:api'])->group(function () {
    // Endpoints accessible by super_admin only
    Route::middleware(['role:super_admin'])->group(function () {
        // Endpoints for role operations
        Route::prefix('roles')->group(function () {
            Route::get('/', ListRolesController::class);
            Route::post('create', CreateRoleController::class);
            Route::post('assign', AssignRoleController::class);
            Route::post('revoke', RevokeRoleController::class);
            Route::delete('delete/{id}', DeleteRoleController::class);
        });

        // Endpoints for permissions operations
        Route::prefix('permissions')->group(function () {
            Route::get('/', ListPermissionsController::class);
            Route::post('create', CreatePermissionController::class);
            Route::delete('delete/{id}', DeletePermissionController::class);
            Route::post('assign-to-user', AssignPermissionToUserController::class);
            Route::post('assign-to-role', AssignPermissionToRoleController::class);
            Route::post('revoke-for-user', RevokePermissionForUserController::class);
            Route::post('revoke-for-role', RevokePermissionForRoleController::class);
        });

        // Endpoints for practice operations
        Route::prefix('practices')->group(function () {
            Route::get('/', ListPracticesController::class);
            Route::post('create', CreatePracticeController::class);
            Route::delete('delete/{id}', DeletePracticeController::class);
            Route::post('assign-to-user', AssignPracticeToUserController::class);
            Route::post('revoke-for-user', RevokePracticeForUserController::class);
        });
    });

    Route::post('sign-policy', SignPolicyController::class);

    // Endpoints for policies
    Route::prefix('policies')->group(function () {
        Route::get('/', ListPoliciesController::class)->name('policies');
    });

    // Routes accessible by super admin and managers only
    Route::middleware(['role:manager|super_admin'])->group(function () {
        // Endpoints for user operations
        Route::prefix('users')->group(function () {
            Route::post('create', CreateUserController::class);
            Route::delete('delete/{id}', DeleteUserController::class);
            Route::get('/', ListUsersController::class);
        });

        // Endpoints for room operations
        Route::prefix('rooms')->group(function () {
            Route::get('/', ListRoomsController::class);
            Route::post('create', CreateRoomController::class);
            Route::delete('delete/{id}', DeleteRoomController::class);
        });

        // Endpoints for CheckList Operations
        Route::prefix('checklists')->group(function () {
            Route::post('create', CreateCheckListController::class);
        });
    });

});