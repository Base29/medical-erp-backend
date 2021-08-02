<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class DeletePermissionController extends Controller
{
    public function __invoke($id)
    {
        $permission = Permission::find($id);

        // Check if permission exists
        if (!$permission) {
            return response([
                'success' => false,
                'message' => 'Permission not found with the provided id ' . $id,
            ]);
        }

        // Delete permission
        $permission->delete();

        return response([
            'success' => true,
            'message' => 'Permission deleted successfully',
        ]);
    }
}