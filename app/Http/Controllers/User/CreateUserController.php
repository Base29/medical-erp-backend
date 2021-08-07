<?php

namespace App\Http\Controllers\User;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUserController extends Controller
{
    public function __invoke(Request $request)
    {
        // Validation rules
        $rules = [
            'email' => 'required|email',
            'name' => 'required',
            'password' => 'required|confirmed',
        ];

        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            // Return error messages against $rules
            return CustomValidation::error_messages($rules, $validator);
        }

        // Check if the user already exists
        $user_exists = User::where('email', $request->email)->first();
        if ($user_exists) {
            return response([
                'success' => false,
                'message' => 'User already exists with email ' . $request->email,
            ], 409);
        }

        // Create user
        $user = new User();
        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->save();

        return $user ?
        response([
            'success' => true,
            'message' => 'New user created with email ' . $user->email,
        ], 200)
        :
        response([
            'success' => false,
            'message' => 'Something went wrong while creating user',
        ], 400);
    }
}