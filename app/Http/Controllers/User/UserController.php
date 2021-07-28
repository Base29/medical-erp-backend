<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function create_user(Request $request)
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
            ray($validator->errors()->all());
            return response([
                'success' => false,
                'message' => 'All fields are required',
            ], 422);
        }

        // Check if the user already exists
        $user_exists = User::where('email', $request->email)->first();
        if ($user_exists) {
            return response([
                'success' => false,
                'message' => 'User already exists with email ' . $request->email,
            ]);
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
        ])
        :
        response([
            'success' => false,
            'message' => 'Something went wrong while creating user',
        ]);
    }
}