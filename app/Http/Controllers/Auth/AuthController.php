<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthenticateUserRequest;
use App\Http\Requests\Auth\ResetPasswordLinkRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Method for Authenticating the user
    public function login(AuthenticateUserRequest $request)
    {
        try {
            // Checking if the user exists in the database
            $user = User::where('email', $request->email)
                ->with(['profile', 'positionSummary', 'contractSummary', 'roles', 'practices'])
                ->firstOrFail();

            // Check if the user is active
            if (!$user->is_active) {
                return Response::fail([
                    'code' => 400,
                    'message' => ResponseMessage::userNotActive($user->email),
                ]);
            }

            if (!$user || !Hash::check($request->password, $user->password)) {

                return Response::fail([
                    'code' => 401,
                    'message' => ResponseMessage::invalidCredentials(),
                ]);
            }

            // Generating JWT token from provided creds
            $token = JWTAuth::attempt($request->only('email', 'password'));

            // Adding token to user array
            Arr::add($user, 'token', $token);

            // Return response
            return Response::success(['user' => $user]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for logging out the user
    public function logout()
    {
        auth()->logout(true);
        return Response::success(['message' => ResponseMessage::logout()]);
    }

    // Method for resetting password
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {

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
                return Response::fail(['message' => ResponseMessage::invalidToken(), 'code' => 401]);
            }

            return Response::success(['message' => ResponseMessage::passwordResetSuccess()]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for generating reset password link
    public function generateResetPasswordLink(ResetPasswordLinkRequest $request)
    {
        try {
            Password::sendResetLink($request->only('email'));

            return Response::success(['message' => ResponseMessage::passwordResetLink($request->email)]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for verifying user token
    public function verifyToken()
    {
        try {

            $user = JWTAuth::parseToken()->authenticate();

            // Add token to the response
            $userWithToken = Arr::add($user->where('id', $user->id)->with('profile', 'positionSummary', 'contractSummary', 'roles', 'practices')->firstOrFail(), 'token', request()->token);

            return Response::success(['user' => $userWithToken]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}