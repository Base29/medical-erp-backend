<?php

/**
 * Password Reset Link Controller is responsible for generating reset password link and email it to the user
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class PasswordResetLinkController extends Controller
{
    public function __invoke(Request $request)
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
}