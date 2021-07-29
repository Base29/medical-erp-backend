<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;

class DeleteUserController extends Controller
{
    public function __invoke($id)
    {
        // Check if the user exists with the provided $id
        $user = User::find($id);

        if (!$user) {
            return response([
                'success' => false,
                'message' => 'No user found with the provided id ' . $id,
            ]);
        }

        // Delete user with the provided $id
        $user->delete();
        return response([
            'success' => true,
            'message' => 'User delete successfully',
        ]);
    }
}