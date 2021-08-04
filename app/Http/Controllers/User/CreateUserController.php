<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
            $errors = $validator->errors();

            // Return error messages for email
            if (Arr::has($errors->messages(), 'email')) {
                return response([
                    'success' => false,
                    'message' => $errors->messages()['email'][0],
                ], 422);
            }

            // Return error messages for name
            if (Arr::has($errors->messages(), 'name')) {
                return response([
                    'success' => false,
                    'message' => $errors->messages()['name'][0],
                ], 422);
            }

            // Return error messages for password
            if (Arr::has($errors->messages(), 'password')) {
                return response([
                    'success' => false,
                    'message' => $errors->messages()['password'][0],
                ], 422);
            }
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