<?php

/**
 * Service for Authentication
 */

namespace App\Services\Auth;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Models\User;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    // Authenticate user
    public function authenticate($request)
    {
        // Checking if the user exists in the database
        $user = User::where('email', $request->email)
            ->with(['profile.hiringRequest', 'positionSummary', 'contractSummary', 'roles', 'practices', 'employmentCheck', 'workPatterns.workTimings', 'locumNotes', 'qualifications', 'miscInfo', 'education', 'employmentHistories', 'references', 'legal'])
            ->withCount(['courses', 'overdueCourses', 'completedCourses', 'inProgressCourses'])
            ->firstOrFail();

        // Check if the user is active
        if (!$user->is_active) {
            throw new Exception(ResponseMessage::userNotActive($user->email), Response::HTTP_FORBIDDEN);
        }

        // Check if $user is blacklisted
        if ($user->is_blacklisted) {
            throw new Exception(ResponseMessage::customMessage('User ' . $user->email . ' is blacklisted. Please contact HQ.'), Response::HTTP_FORBIDDEN);
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new Exception(ResponseMessage::invalidCredentials(), Response::HTTP_UNAUTHORIZED);
        }

        // Generating JWT token from provided credentials
        $token = JWTAuth::attempt($request->only('email', 'password'));

        // Adding token to user array
        Arr::add($user, 'token', $token);

        // Return $user
        return $user;
    }

    // Logout
    public function logout()
    {
        return auth()->logout(true);
    }

    // Reset Password
    public function resetPassword($request)
    {
        // Initiating password reset
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        // Check if the provided reset token or user is valid
        if ($status === Password::INVALID_TOKEN || $status === Password::INVALID_USER) {
            throw new Exception(ResponseMessage::invalidToken(), Response::HTTP_UNAUTHORIZED);
        }
    }

    // Reset password link
    public function resetPasswordLink($request)
    {
        return Password::sendResetLink($request->only('email'));
    }

    // Verify token
    public function verifyToken()
    {
        $user = JWTAuth::parseToken()->authenticate();

        // Add token to the response
        $userWithToken = Arr::add($user->where('id', $user->id)
                ->with('profile', 'positionSummary', 'contractSummary', 'roles.itPolicies', 'roles.handbooks', 'practices')
                ->firstOrFail(), 'token', request()->token);

        return $userWithToken;
    }
}