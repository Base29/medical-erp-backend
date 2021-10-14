<?php

namespace App\Http\Controllers\User;

use App\Helpers\CustomValidation;
use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Method for creating user
    public function create(Request $request)
    {
        // Validation rules
        $rules = [
            'email' => 'required|email|unique:users,email',
            'name' => 'required',
            'password' => 'required|confirmed',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Create user
        $user = new User();
        $user->email = $request->email;
        $user->name = $request->name;
        $user->password = Hash::make($request->password);
        $user->save();

        return Response::success(['user' => $user]);
    }

    // Method for deleting user
    public function delete($id)
    {
        // Check if the user exists with the provided $id
        $user = User::find($id);

        if (!$user) {
            return Response::fail([
                'message' => 'User with ID ' . $id . ' not found',
                'code' => 404,
            ]);
        }

        // Delete user with the provided $id
        $user->delete();

        return Response::success(['message' => 'User delete successfully']);
    }

    // Method for fetching users
    public function fetch()
    {
        // Fetching all the users from database
        $users = User::with('roles', 'practices')->latest()->paginate(10);

        return Response::success(['users' => $users]);
    }
}