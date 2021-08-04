<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;

class ListUsersController extends Controller
{
    public function __invoke()
    {
        // Fetching all the users from database
        $users = User::with('roles', 'practices')->paginate(10);

        return response([
            'success' => true,
            'users' => $users,
        ], 200);
    }
}