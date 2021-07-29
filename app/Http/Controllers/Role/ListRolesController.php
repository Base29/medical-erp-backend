<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class ListRolesController extends Controller
{
    public function __invoke()
    {
        // Fetching roles
        $roles = Role::with('users')->paginate(10);
        return response([
            'success' => true,
            'roles' => $roles,
        ]);
    }
}