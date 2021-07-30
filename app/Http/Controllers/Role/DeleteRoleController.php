<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class DeleteRoleController extends Controller
{
    public function __invoke($id)
    {
        // Check if the user exists with the provided $id
        $role = Role::find($id);

        if (!$role) {
            return response([
                'success' => false,
                'message' => 'No role found with the provided id ' . $id,
            ]);
        }

        // Delete user with the provided $id
        $role->delete();
        return response([
            'success' => true,
            'message' => 'Role delete successfully',
        ]);
    }
}