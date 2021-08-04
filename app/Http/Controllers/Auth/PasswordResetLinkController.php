<?php

/**
 * Password Reset Link Controller is responsible for generating reset password link and email it to the user
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class PasswordResetLinkController extends Controller
{
    public function __invoke(Request $request)
    {
        $credentials = request()->validate(['email' => 'required|email']);

        // Validation rules
        $rules = [
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
        }

        $user = User::where('email', $request->only('email'))->first();

        if (!$user) {
            return response([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        Password::sendResetLink($request->email);

        return response([
            'success' => true,
            'message' => 'Reset password link sent on your email id.',
        ], 200);
    }
}