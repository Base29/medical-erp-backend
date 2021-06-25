<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        ray($request->all());
        // Validation rules
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
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

        /**
         * For some reason Laravel's built-in current_password rule for Validator is not working.
         * So for sake of consistency in the responses I am opting to use custom validaion to check if the user
         * exists in the database and if the provided password is correct.
         */

        //TODO: In future switch to Laravel's built-in validation after checking current_password rule is working as expected.

        // Checking if the user exists in the database
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response([
                'success' => false,
                'message' => 'User does not exists',
            ]);
        }

        // Check if the provided password is correct
        if (!Hash::check($request->password, $user->password)) {
            return response([
                'success' => false,
                'message' => 'Incorrect Password',
            ]);
        }

        // Generating JWT token from provided creds
        $token = JWTAuth::attempt($request->only('email', 'password'));
        if (!$token) {
            return response([
                'success' => false,
                'message' => 'Invalid Credentials',
            ], 401);
        }

        // Adding token to user array
        $userArr = Arr::add($user, 'token', $token);
        ray($userArr);
        return response([
            'success' => true,
            'user' => $userArr,
        ]);
    }
}