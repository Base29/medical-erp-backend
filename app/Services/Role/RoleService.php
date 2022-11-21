<?php
namespace App\Services\Role;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Models\Role;
use App\Models\User;
use Exception;

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

        return Response::success([
            'code' => Response::HTTP_CREATED,
            'role' => $role->name,
        ]);
    }

    // Delete role
    public function deleteRole($id)
    {
        // Check if the user exists with the provided $id
        $role = Role::findOrFail($id);

        if (!$role) {
            throw new Exception(ResponseMessage::notFound('Role', $id, false), Response::HTTP_NOT_FOUND);
        }

        // Delete user with the provided $id
        $role->delete();

        return Response::success(['role' => $role]);
    }

    // Fetch roles
    public function fetchRoles()
    {
        // Fetching roles
        $roles = Role::with('users', 'itPolicies', 'handbooks', 'inductionChecklists')->latest()->get();

        return Response::success(['roles' => $roles]);
    }

    // Assign role to user
    public function assignRoleToUser($request)
    {
        // Get User
        $user = User::where('email', $request->email)->firstOrFail();

        // Check if the user has assigned the provided role
        if ($user->hasRole($request->role)) {
            throw new Exception(ResponseMessage::alreadyAssigned($request->role, $user->email), Response::HTTP_CONFLICT);
        }

        // Assigning role to the user
        $user->assignRole($request->role);

        return Response::success([
            'code' => Response::HTTP_OK,
            'message' => ResponseMessage::assigned($request->role, $user->email),
        ]);
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
            throw new Exception(ResponseMessage::notAssigned($role->name, $user->email), Response::HTTP_CONFLICT);
        }

        // Revoking role
        $user->removeRole($role->name);

        return Response::success([
            'code' => Response::HTTP_OK,
            'message' => ResponseMessage::revoked($role->name, $user->email),
        ]);
    }
}