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

        return Response::success(['user' => $user->with('roles', 'practices')->first()]);
    }
}