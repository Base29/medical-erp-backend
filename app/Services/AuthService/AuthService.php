<?php

/**
 * Service for Authentication
 */

namespace App\Services\AuthService;

use App\Helpers\ResponseMessage;
use App\Models\User;
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
            ->with(['profile', 'positionSummary', 'contractSummary', 'roles', 'practices'])
            ->firstOrFail();

        // Check if the user is active
        if (!$user->is_active) {
            throw new \Exception(ResponseMessage::userNotActive($user->email));
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw new \Exception(ResponseMessage::invalidCredentials());
        }

        // Generating JWT token from provided creds
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
            throw new \Exception(ResponseMessage::invalidToken());
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
        $userWithToken = Arr::add($user->where('id', $user->id)->with('profile', 'positionSummary', 'contractSummary', 'roles', 'practices')->firstOrFail(), 'token', request()->token);

        return $userWithToken;
    }
}