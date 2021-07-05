<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function generateResetLink(Request $request)
    {
        $credentials = request()->validate(['email' => 'required|email']);

        $user = User::where('email', $request->only('email'))->first();

        if (!$user) {
            return response([
                'success' => false,
                'message' => 'Invalid user',
            ]);
        }

        Password::sendResetLink($credentials);

        return response([
            'success' => true,
            'message' => 'Reset password link sent on your email id.',
        ]);
    }

    public function resetPassword(Request $request)
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

        return $status === Password::INVALID_TOKEN ?
        response(['success' => false, 'message' => 'Invalid Token']) :
        response(['success' => true, 'message' => 'Password reset successfully']);

    }
}