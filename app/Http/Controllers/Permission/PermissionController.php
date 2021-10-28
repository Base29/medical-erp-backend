<?php

namespace App\Http\Controllers\Permission;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Permission\AssignPermissionToRoleRequest;
use App\Http\Requests\Permission\AssignPermissionToUserRequest;
use App\Http\Requests\Permission\CreatePermissionRequest;
use App\Http\Requests\Permission\RevokePermissionForRoleRequest;
use App\Http\Requests\Permission\RevokePermissionForUserRequest;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    // Method for creating a permission
    public function create(CreatePermissionRequest $request)
    {
        try {

            // Create permission
            // $permission = Permission::create(['guard_name' => 'api', 'name' => $request->name]);
            $permission = new Permission();
            $permission->guard_name = 'api';
            $permission->name = $request->name;
            $permission->save();

            return Response::success(['permission' => $permission]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for deleting permission
    public function delete($id)
    {
        try {

            $permission = Permission::findOrFail($id);

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

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }

    }

    // Method for fetching permissions
    public function fetch()
    {

        try {

            // Fetching permissions
            $permissions = Permission::latest()->paginate(10);

            return Response::success(['permissions' => $permissions]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for assigning permission to a role
    public function assignToRole(AssignPermissionToRoleRequest $request)
    {
        try {

            // Check if the role exists
            $role = Role::where('name', $request->role)->firstOrFail();

            // Check if the role already has the provided permission
            $alreadyHasPermission = $role->hasPermissionTo($request->permission);

            if ($alreadyHasPermission) {
                return Response::fail([
                    'message' => ResponseMessage::alreadyAssigned($request->permission, $role->name),
                    'code' => 409,
                ]);
            }

            // Check if the permission exists
            $permission = Permission::where('name', $request->permission)->firstOrFail();

            // Assigning permission to the role
            $role->givePermissionTo($request->permission);

            return Response::success(['role' => ResponseMessage::assigned($permission->name, $role->name)]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for assigning permission to a user
    public function assignToUser(AssignPermissionToUserRequest $request)
    {
        try {

            // Check if the user exists
            $user = User::where('email', $request->email)->firstOrFail();

            // Check if the user already has the provided permission
            $alreadyHasPermission = $user->hasPermissionTo($request->permission);

            if ($alreadyHasPermission) {
                return Response::false([
                    'message' => ResponseMessage::alreadyAssigned($request->permission, $user->name),
                    'code' => 409,
                ]);
            }

            // Check if the permission exists
            $permission = Permission::where('name', $request->permission)->firstOrFail();

            // Assigning permission to the user
            $user->givePermissionTo($request->permission);

            return Response::success(['message' => ResponseMessage::assigned($permission->name, $user->email)]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }

    }

    // Method for revoking permission for role
    public function revokeForRole(RevokePermissionForRoleRequest $request)
    {

        try {

            // Get role
            $role = Role::where('name', $request->role)->firstOrFail();

            // Get permission
            $permission = Permission::where('name', $request->permission)->firstOrFail();

            // Check if the role has the permission that is being revoked
            $roleHasPermission = $role->hasPermissionTo($permission->name);

            if (!$roleHasPermission) {
                return Response::fail([
                    'message' => ResponseMessage::notAssigned($permission->name, $role->name),
                    'code' => 400,
                ]);
            }

            // Revoke permission for the provided role
            $role->revokePermissionTo($permission->name);

            return Response::success(['message' => ResponseMessage::revoked($permission->name, $role->name)]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }

    }

    // Method for revoking permission for user
    public function revokeForUser(RevokePermissionForUserRequest $request)
    {

        try {

            // Get User
            $user = User::where('email', $request->email)->firstOrFail();

            // Get Permission
            $permission = Permission::where('name', $request->permission)->firstOrFail();

            // Check if the user has the permission that is being revoked
            $userHasPermission = $user->hasPermissionTo($permission->name);

            if (!$userHasPermission) {
                return response::fail([
                    'message' => ResponseMessage::notAssigned($permission->name, $user->name),
                    'code' => 400,
                ]);
            }

            // Revoke permission for the provided user
            $user->revokePermissionTo($permission->name);

            return Response::success(['message' => ResponseMessage::revoked($permission->name, $user->name)]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}