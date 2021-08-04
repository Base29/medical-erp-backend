<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignPermissionToRoleController extends Controller
{
    public function __invoke(Request $request)
    {
        ray($request->all());

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

            // Return error messages for role
            if (Arr::has($errors->messages(), 'role')) {
                return response([
                    'success' => false,
                    'message' => $errors->messages()['role'][0],
                ], 422);
            }

            // Return error messages for permission
            if (Arr::has($errors->messages(), 'permission')) {
                return response([
                    'success' => false,
                    'message' => $errors->messages()['permission'][0],
                ], 422);
            }
        }

        // Check if the role exists
        $role = Role::where('name', $request->role)->first();

        if (!$role) {
            return response([
                'success' => false,
                'message' => 'Role ' . $request->role . ' doesn\'t exist',
            ], 404);
        }

        // Check if the role already has the provided permission
        $already_has_permission = $role->hasPermissionTo($request->permission);

        if ($already_has_permission) {
            return response([
                'success' => false,
                'message' => 'Role ' . $role->name . ' already has ' . $request->permission . ' permission',
            ], 409);
        }

        // Check if the permission exists
        $permission = Permission::where('name', $request->permission)->first();

        if (!$permission) {
            return response([
                'success' => false,
                'message' => 'Permission ' . $request->permission . ' doesn\'t exist',
            ], 404);
        }

        // Assigning permission to the role
        $role->givePermissionTo($request->permission);

        return response([
            'success' => true,
            'message' => 'Permission ' . $permission->name . ' assigned to ' . $role->name . ' role',
        ], 200);

    }
}