<?php
namespace App\Services\Permission;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionService
{
    // Create Permission
    public function createPermission($request)
    {
        // Create permission
        $permission = new Permission();
        $permission->guard_name = 'api';
        $permission->name = $request->name;
        $permission->save();

        // Return permission
        return $permission;
    }

    // Delete permission
    public function deletePermission($id)
    {
        $permission = Permission::findOrFail($id);

        // Check if permission exists
        if (!$permission) {
            throw new \Exception(ResponseMessage::notFound('Permission', $id, false));
        }

        // Delete permission
        $permission->delete();
    }

    // Fetch Permissions
    public function fetchPermissions()
    {
        return Permission::latest()->paginate(10);
    }

    // Assign permission to role
    public function assignPermissionToRole($request)
    {
        // Check if the role exists
        $role = Role::where('name', $request->role)->firstOrFail();

        // Check if the role already has the provided permission
        $alreadyHasPermission = $role->hasPermissionTo($request->permission);

        if ($alreadyHasPermission) {
            throw new \Exception(ResponseMessage::alreadyAssigned($request->permission, $role->name));
        }

        // Check if the permission exists
        $permission = Permission::where('name', $request->permission)->firstOrFail();

        // Assigning permission to the role
        $role->givePermissionTo($permission->name);

        return Response::success(['message' => ResponseMessage::assigned($permission->name, $role->name)]);
    }

    // Assign permission to user
    public function assignPermissionToUser($request)
    {
        // Check if the user exists
        $user = User::where('email', $request->email)->firstOrFail();

        // Check if the user already has the provided permission
        $alreadyHasPermission = $user->hasPermissionTo($request->permission);

        if ($alreadyHasPermission) {
            throw new \Exception(ResponseMessage::alreadyAssigned($request->permission, $user->name));
        }

        // Check if the permission exists
        $permission = Permission::where('name', $request->permission)->firstOrFail();

        // Assigning permission to the user
        $user->givePermissionTo($permission->name);

        return Response::success(['message' => ResponseMessage::assigned($permission->name, $user->email)]);
    }

    // Revoke permission for user
    public function revokePermissionForUser($request)
    {
        // Get User
        $user = User::where('email', $request->email)->firstOrFail();

        // Get Permission
        $permission = Permission::where('name', $request->permission)->firstOrFail();

        // Check if the user has the permission that is being revoked
        $userHasPermission = $user->hasPermissionTo($permission->name);

        if (!$userHasPermission) {
            throw new \Exception(ResponseMessage::notAssigned($permission->name, $user->name));
        }

        // Revoke permission for the provided user
        $user->revokePermissionTo($permission->name);

        return Response::success(['message' => ResponseMessage::revoked($permission->name, $user->name)]);
    }

    // Revoke permission for role
    public function revokePermissionForRole($request)
    {
        // Get role
        $role = Role::where('name', $request->role)->firstOrFail();

        // Get permission
        $permission = Permission::where('name', $request->permission)->firstOrFail();

        // Check if the role has the permission that is being revoked
        $roleHasPermission = $role->hasPermissionTo($permission->name);

        if (!$roleHasPermission) {
            throw new \Exception(ResponseMessage::notAssigned($permission->name, $role->name));
        }

        // Revoke permission for the provided role
        $role->revokePermissionTo($permission->name);

        return Response::success(['message' => ResponseMessage::revoked($permission->name, $role->name)]);
    }
}