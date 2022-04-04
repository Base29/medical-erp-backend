<?php
namespace App\Services\Role;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RoleService
{
    // Create role
    public function createRole($request)
    {
        // Create role
        $role = new Role();
        $role->guard_name = 'api';
        $role->name = $request->name;
        $role->save();

        return Response::success(['role' => $role->name]);
    }

    // Delete role
    public function deleteRole($id)
    {
        // Check if the user exists with the provided $id
        $role = Role::findOrFail($id);

        if (!$role) {
            throw new \Exception(ResponseMessage::notFound('Role', $id, false));
        }

        // Delete user with the provided $id
        $role->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('Role')]);
    }

    // Fetch roles
    public function fetchRoles()
    {
        // Fetching roles
        $roles = Role::with('users')->latest()->paginate(10);

        return Response::success(['roles' => $roles]);
    }

    // Assign role to user
    public function assignRoleToUser($request)
    {
        // Get User
        $user = User::where('email', $request->email)->firstOrFail();

        // Check if the user has assigned the provided role
        if ($user->hasRole($request->role)) {
            throw new \Exception(ResponseMessage::alreadyAssigned($request->role, $user->email));
        }

        // Assigning role to the user
        $user->assignRole($request->role);

        return Response::success(['message' => ResponseMessage::assigned($request->role, $user->email)]);
    }

    // Revoke role for user
    public function revokeRoleForUser($request)
    {
        // Get User
        $user = User::where('email', $request->email)->firstOrFail();

        // Get Role
        $role = Role::where('name', $request->role)->firstOrFail();

        // Check if the user has assigned the provided role
        if (!$user->hasRole($role->name)) {
            throw new \Exception(ResponseMessage::notAssigned($role->name, $user->email));
        }

        // Revoking role
        $user->removeRole($role->name);

        return Response::success(['message' => ResponseMessage::revoked($role->name, $user->email)]);
    }
}