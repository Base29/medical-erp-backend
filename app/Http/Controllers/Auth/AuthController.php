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
        // Checking if the user exists in the database
        $user = User::where('email', $request->email)->with(['roles', 'practices'])->first();

        if (!$user || !Hash::check($request->password, $user->password)) {

            return Response::fail([
                'code' => 401,
                'message' => ResponseMessage::invalidCredentials(),
            ]);
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
        Arr::add($user, 'token', $token);

        // Return response
        return Response::success(['user' => $user]);
    }

    // Method for logging out the user
    public function logout()
    {
        auth()->logout(true);
        return Response::success(['message' => ResponseMessage::logout()]);
    }

    // Method for resetting password
    public function reset_password(ResetPasswordRequest $request)
    {
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
        Response::fail(['message' => ResponseMessage::invalidToken(), 'code' => 401]) :
        Response::success(['message' => ResponseMessage::passwordResetSuccess()]);
    }

    // Method for generating reset password link
    public function generate_reset_password_link(ResetPasswordLinkRequest $request)
    {
        Password::sendResetLink($request->only('email'));

        return Response::success(['message' => ResponseMessage::passwordResetLink($request->email)]);
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
        return Response::success(['user' => $user_with_practices]);
    }
}