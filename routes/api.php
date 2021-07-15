<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\RoleController;
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

Route::middleware(['auth:api'])->group(function () {
    Route::post('logout', [LogoutController::class, 'logout'])->name('logout');
    Route::post('create-role', [RoleController::class, 'create_role']);
    Route::post('assign-role', [RoleController::class, 'assign_role']);
    Route::post('revoke-role', [RoleController::class, 'revoke_role']);
});