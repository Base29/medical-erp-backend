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

        // Checking if the user exists in the database
        $user = User::where('email', $request->email)->with(['roles', 'practices'])->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'success' => false,
                'message' => 'Invalid Credentials',
            ], 401);
        }

        $user->roles->makeHidden([
            'pivot',
            'created_at',
            'updated_at',
            'guard_name',
        ]);

        // Generating JWT token from provided creds
        $token = JWTAuth::attempt($request->only('email', 'password'));

        // Adding token to user array
        $userArr = Arr::add($user, 'token', $token);

        // Return response
        return response([
            'success' => true,
            'user' => $userArr,
        ], 200);
    }
}