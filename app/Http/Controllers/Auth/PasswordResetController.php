<?php

/**
 * Password Reset Controller is responsible for resetting the password for the provided user account
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function __invoke(Request $request)
    {

        // Validation rules
        $rules = [
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
            'email' => 'required|email',
        ];

        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            $errors = $validator->errors();

            // Return error messages for email
            if (Arr::has($errors->messages(), 'email')) {
                return response([
                    'success' => false,
                    'message' => $errors->messages()['email'][0],
                ], 422);
            }

            // Return error messages for password
            if (Arr::has($errors->messages(), 'password')) {
                return response([
                    'success' => false,
                    'message' => $errors->messages()['password'][0],
                ], 422);
            }

            // Return error messages for token
            if (Arr::has($errors->messages(), 'token')) {
                return response([
                    'success' => false,
                    'message' => $errors->messages()['token'][0],
                ], 422);
            }
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
}