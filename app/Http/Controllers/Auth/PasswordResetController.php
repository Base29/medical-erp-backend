<?php

/**
 * Password Reset Controller is responsible for resetting the password for the provided user account
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
            'email' => 'required|email',
        ]);

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
        response(['success' => false, 'message' => 'Invalid Token or User']) :
        response(['success' => true, 'message' => 'Password reset successfully']);
    }
}