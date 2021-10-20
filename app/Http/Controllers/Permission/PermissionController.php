<?php

namespace App\Http\Controllers\Permission;

use App\Helpers\CustomValidation;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
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
            return Response::fail([
                'message' => ResponseMessage::alreadyExists('Permission'),
                'code' => 409,
            ]);
        }

        // Create permission
        $permission = Permission::create(['guard_name' => 'api', 'name' => $request->name]);

        return Response::success(['permission' => $permission]);
    }

    // Method for deleting permission
    public function delete($id)
    {
        $permission = Permission::find($id);

        // Check if permission exists
        if (!$permission) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Permission', $id, false),
                'code' => 404,
            ]);
        }

        // Delete permission
        $permission->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('Permission')]);
    }

    // Method for fetching permissions
    public function fetch()
    {
        // Fetching permissions
        $permissions = Permission::paginate(10);

        return Response::success(['permissions' => $permissions]);
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
            return Response::fail([
                'message' => ResponseMessage::notFound('Role', $request->role, false),
                'code' => 404,
            ]);
        }

        // Check if the role already has the provided permission
        $already_has_permission = $role->hasPermissionTo($request->permission);

        if ($already_has_permission) {
            return Response::fail([
                'message' => ResponseMessage::alreadyAssigned($request->permission, $role->name),
                'code' => 409,
            ]);
        }

        // Check if the permission exists
        $permission = Permission::where('name', $request->permission)->first();

        if (!$permission) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Permission', $request->permission, false),
                'code' => 404,
            ]);
        }

        // Assigning permission to the role
        $role->givePermissionTo($request->permission);

        return Response::success(['role' => ResponseMessage::assigned($permission->name, $role->name)]);
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
            return Response::fail([
                'message' => ResponseMessage::notFound('User', $request->email, true),
                'code' => 404,
            ]);
        }

        // Check if the user already has the provided permission
        $already_has_permission = $user->hasPermissionTo($request->permission);

        if ($already_has_permission) {
            return Response::false([
                'message' => ResponseMessage::alreadyAssigned($request->permission, $user->name),
                'code' => 409,
            ]);
        }

        // Check if the permission exists
        $permission = Permission::where('name', $request->permission)->first();

        if (!$permission) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Permission', $request->permission, false),
                'code' => 404,
            ]);
        }

        // Assigning permission to the user
        $user->givePermissionTo($request->permission);

        return Response::success(['message' => ResponseMessage::assigned($permission->name, $user->email)]);
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
            return Response::fail([
                'message' => ResponseMessage::notFound('Role', $request->role, false),
                'code' => 404,
            ]);
        }

        // Check if the permission exists which is being revoked for the role
        $permission = Permission::where('name', $request->permission)->first();

        if (!$permission) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Permission', $request->permission, false),
                'code' => 404,
            ]);
        }

        // Check if the role has the permission that is being revoked
        $role_has_permission = $role->hasPermissionTo($permission->name);

        if (!$role_has_permission) {
            return Response::fail([
                'message' => ResponseMessage::notAssigned($permission->name, $role->name),
                'code' => 400,
            ]);
        }

        // Revoke permission for the provided role
        $role->revokePermissionTo($permission->name);

        return Response::success(['message' => ResponseMessage::revoked($permission->name, $role->name)]);
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
            return Response::fail([
                'message' => ResponseMessage::notFound('User', $request->email, true),
                'code' => 404,
            ]);
        }

        // Check if the permission exists which is being revoked for the user
        $permission = Permission::where('name', $request->permission)->first();

        if (!$permission) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Permission', $request->permission, false),
                'code' => 404,
            ]);
        }

        // Check if the user has the permission that is being revoked
        $user_has_permission = $user->hasPermissionTo($permission->name);

        if (!$user_has_permission) {
            return response::fail([
                'message' => ResponseMessage::notAssigned($permission->name, $user->name),
                'code' => 400,
            ]);
        }

        // Revoke permission for the provided user
        $user->revokePermissionTo($permission->name);

        return Response::success(['message' => ResponseMessage::revoked($permission->name, $user->name)]);
    }
}