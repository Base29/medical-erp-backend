<?php

namespace App\Http\Controllers\User;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Method for creating user
    public function create(CreateUserRequest $request)
    {
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
                'message' => ResponseMessage::notFound('User', $id, false),
                'code' => 404,
            ]);
        }

        // Delete user with the provided $id
        $user->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('User')]);
    }

    // Method for fetching users
    public function fetch()
    {
        // Fetching all the users from database
        $users = User::with('roles', 'practices')->latest()->paginate(10);

        return Response::success(['users' => $users]);
    }
}