<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function generatePasswordToken(Request $request)
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

        return response([
            'success' => true,
            'message' => 'Reset Password Endpoint',
        ]);

    }
}