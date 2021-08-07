<?php

/**
 * Password Reset Link Controller is responsible for generating reset password link and email it to the user
 */

namespace App\Http\Controllers\Auth;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class PasswordResetLinkController extends Controller
{
    public function __invoke(Request $request)
    {
        // Validation rules
        $rules = [
            'email' => 'required|email',
        ];

        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            // Return error messages against $rules
            return CustomValidation::error_messages($rules, $validator);
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
}