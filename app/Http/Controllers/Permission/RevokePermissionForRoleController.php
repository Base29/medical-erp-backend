<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RevokePermissionForRoleController extends Controller
{
    public function __invoke(Request $request)
    {
        // Validation rules
        $rules = [
            'role' => 'required',
            'permission' => 'required',
        ];

        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (Arr::has($errors->messages(), 'email')) {
                return response([
                    'success' => false,
                    'message' => $errors->messages()['email'][0],
                ], 422);
            } else if (Arr::has($errors->messages(), 'permission')) {
                return response([
                    'success' => false,
                    'message' => $errors->messages()['permission'][0],
                ], 422);
            }
        }

        // Check if the role exists for which the permission is being revoked
        $role = Role::where('name', $request->role)->first();

        if (!$role) {
            return response([
                'success' => false,
                'message' => 'Role ' . $request->role . ' doesn\'t exists',
            ], 404);
        }

        // Check if the permission exists which is being revoked for the role
        $permission = Permission::where('name', $request->permission)->first();

        if (!$permission) {
            return response([
                'success' => false,
                'message' => 'Permission ' . $request->permission . ' doesn\'t exist',
            ], 404);
        }

        // Check if the role has the permission that is being revoked
        $role_has_permission = $role->hasPermissionTo($permission->name);

        if (!$role_has_permission) {
            return response([
                'success' => false,
                'message' => 'Role ' . $role->name . ' doesn\'t have ' . $permission->name . ' permission',
            ], 400);
        }

        // Revoke permission for the provided role
        $role->revokePermissionTo($permission->name);

        return response([
            'success' => true,
            'message' => 'Permission ' . $permission->name . ' revoked for role ' . $role->name,
        ]);
    }
}