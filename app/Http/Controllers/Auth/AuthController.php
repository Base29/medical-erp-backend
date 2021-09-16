<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Method for Authenticating the user
    public function login(Request $request)
    {
        // Validation rules
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Checking if the user exists in the database
        $user = User::where('email', $request->email)->with(['roles', 'practices'])->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'success' => false,
                'message' => 'Invalid Credentials',
            ], 401);
        }

        $user->roles->makeHidden([
            'pivot',
            'created_at',
            'updated_at',
            'guard_name',
        ]);

        // Generating JWT token from provided creds
        $token = JWTAuth::attempt($request->only('email', 'password'));

        // Adding token to user array
        $userArr = Arr::add($user, 'token', $token);

        // Return response
        return response([
            'success' => true,
            'user' => $userArr,
        ], 200);
    }

    // Method for logging out the user
    public function logout()
    {
        auth()->logout(true);
        return response([
            'success' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }

    // Method for resetting password
    public function reset_password(Request $request)
    {
        // Validation rules
        $rules = [
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
            'email' => 'required|email',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::INVALID_TOKEN || $status === Password::INVALID_USER ?
        response(['success' => false, 'message' => 'Invalid Token or User'], 401) :
        response(['success' => true, 'message' => 'Password reset successfully'], 200);
    }

    // Method for generating reset password link
    public function generate_reset_password_link(Request $request)
    {
        // Validation rules
        $rules = [
            'email' => 'required|email',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Check if the user exists
        $user = User::where('email', $request->only('email'))->first();

        if (!$user) {
            return response([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        Password::sendResetLink($request->only('email'));

        return response([
            'success' => true,
            'message' => 'Reset password link sent on your email id.',
        ], 200);
    }

    // Method for verifying user token
    public function verify_token()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $user_roles = $user->roles
            ->makeHidden([
                'pivot',
                'created_at',
                'updated_at',
                'guard_name',
            ]);

        $user_practices = $user->practices
            ->makeHidden([
                'pivot',
                'created_at',
                'updated_at',
                'deleted_at',
            ]);
        $user_with_roles = Arr::add($user, 'roles', $user_roles);
        $user_with_practices = Arr::add($user_with_roles, 'practices', $user_practices);
        return response([
            'success' => true,
            'user' => $user_with_practices,
        ], 200);
    }
}