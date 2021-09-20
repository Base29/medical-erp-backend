<?php

namespace App\Http\Controllers\Permission;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    // Method for creating a permission
    public function create(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Check if the permission already exists
        $permission_exists = Permission::where('name', $request->name)->first();

        if ($permission_exists) {
            return response([
                'success' => false,
                'message' => 'Permission ' . $request->name . ' already exists',
            ], 409);
        }

        // Create permission
        $permission = Permission::create(['guard_name' => 'api', 'name' => $request->name]);

        return !$permission ? response([
            'success' => false,
            'message' => 'Role not created. Something went wrong.',
        ], 400) :
        response([
            'success' => true,
            'message' => $permission->name . ' permission created successfully',
        ], 200);
    }

    // Method for deleting permission
    public function delete($id)
    {
        $permission = Permission::find($id);

        // Check if permission exists
        if (!$permission) {
            return response([
                'success' => false,
                'message' => 'Permission not found with the provided id ' . $id,
            ], 404);
        }

        // Delete permission
        $permission->delete();

        return response([
            'success' => true,
            'message' => 'Permission deleted successfully',
        ], 200);
    }

    // Method for fetching permissions
    public function fetch()
    {
        // Fetching permissions
        $permissions = Permission::paginate(10);

        return response([
            'success' => true,
            'permissions' => $permissions,
        ], 200);
    }

    // Method for assigning permission to a role
    public function assign_to_role(Request $request)
    {
        // Validation rules
        $rules = [
            'role' => 'required',
            'permission' => 'required',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
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

    // Method for assigning permission to a user
    public function assign_to_user(Request $request)
    {
        // Validation rules
        $rules = [
            'email' => 'required|email',
            'permission' => 'required',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Check if the user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response([
                'success' => false,
                'message' => 'User doesn\'t exist with the provided email ' . $request->email,
            ], 404);
        }

        // Check if the user already has the provided permission
        $already_has_permission = $user->hasPermissionTo($request->permission);

        if ($already_has_permission) {
            return response([
                'success' => false,
                'message' => 'User ' . $user->email . ' already has ' . $request->permission . ' permission',
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

        // Assigning permission to the user
        $user->givePermissionTo($request->permission);

        return response([
            'success' => true,
            'message' => 'Permission ' . $permission->name . ' assigned to ' . $user->email,
        ], 200);
    }

    // Method for revoking permission for role
    public function revoke_for_role(Request $request)
    {
        // Validation rules
        $rules = [
            'role' => 'required',
            'permission' => 'required',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
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
        ], 200);
    }

    // Method for revoking permission for user
    public function revoke_for_user(Request $request)
    {
        // Validation rules
        $rules = [
            'email' => 'required|email',
            'permission' => 'required',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Check if the user exists for which the permission is being revoked
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response([
                'success' => false,
                'message' => 'User ' . $request->email . ' doesn\'t exists',
            ], 404);
        }

        // Check if the permission exists which is being revoked for the user
        $permission = Permission::where('name', $request->permission)->first();

        if (!$permission) {
            return response([
                'success' => false,
                'message' => 'Permission ' . $request->permission . ' doesn\'t exist',
            ], 404);
        }

        // Check if the user has the permission that is being revoked
        $user_has_permission = $user->hasPermissionTo($permission->name);

        if (!$user_has_permission) {
            return response([
                'success' => false,
                'message' => 'User ' . $user->email . ' doesn\'t have ' . $permission->name . ' permission',
            ], 400);
        }

        // Revoke permission for the provided user
        $user->revokePermissionTo($permission->name);

        return response([
            'success' => true,
            'message' => 'Permission ' . $permission->name . ' revoked for user ' . $user->email,
        ], 200);
    }
}