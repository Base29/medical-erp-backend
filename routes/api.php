<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\VerifyTokenController;
use App\Http\Controllers\Policy\PolicyController;
use App\Http\Controllers\Practice\PracticeController;
use App\Http\Controllers\Role\AssignRoleController;
use App\Http\Controllers\Role\CreateRoleController;
use App\Http\Controllers\Role\DeleteRoleController;
use App\Http\Controllers\Role\ListRolesController;
use App\Http\Controllers\Role\RevokeRoleController;
use App\Http\Controllers\Signature\SignatureController;
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

Route::post('login', [LoginController::class, 'login'])->name('login');
Route::post('forgot-password', [ForgotPasswordController::class, 'generateResetLink'])->name('forgot.password');
Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('reset.password');
Route::post('verify-token', [VerifyTokenController::class, 'verify_token']);

Route::middleware(['auth:api'])->group(function () {
    //TODO: Add prefixes for the all of the API endpoints
    Route::post('logout', [LogoutController::class, 'logout'])->name('logout');

    // Endpoints accessible by super_admin only
    Route::middleware(['role:super_admin'])->group(function () {
        // Endpoints for role operations
        Route::prefix('roles')->group(function () {
            Route::get('/', ListRolesController::class);
            Route::post('create', CreateRoleController::class);
            Route::post('assign', AssignRoleController::class);
            Route::post('revoke', RevokeRoleController::class);
            Route::delete('/{id}', DeleteRoleController::class);
        });
    });
    Route::post('assign-practice', [PracticeController::class, 'assign_practice']);
    Route::post('assign-policy', [PracticeController::class, 'assign_policy']);
    Route::get('practices', [PracticeController::class, 'get_practices']);
    Route::post('sign-policy', [SignatureController::class, 'sign_policy']);
    Route::get('policies', [PolicyController::class, 'fetch_policies']);

    Route::middleware(['role:manager|super_admin'])->group(function () {
        // Endpoints for user operations
        Route::prefix('users')->group(function () {
            Route::post('create', CreateUserController::class);
            Route::delete('/{id}', DeleteUserController::class);
            Route::get('/', ListUsersController::class);
        });
    });

});