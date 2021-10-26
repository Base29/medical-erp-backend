<?php

namespace App\Http\Controllers\Role;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\AssignRoleToUserRequest;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\RevokeRoleForUserRequest;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    // Method for create role
    public function create(CreateRoleRequest $request)
    {

        try {

            // Create role
            $role = new Role();
            $role->guard_name = 'api';
            $role->name = $request->name;
            $role->save();

            return Response::success(['role' => $role->name]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for deleting role
    public function delete($id)
    {
        try {

            // Check if the user exists with the provided $id
            $role = Role::findOrFail($id);

            if (!$role) {
                return Response::fail([
                    'message' => ResponseMessage::notFound('Role', $id, false),
                    'code' => 404,
                ]);
            }

            // Delete user with the provided $id
            $role->delete();

            return Response::success(['message' => ResponseMessage::deleteSuccess('Role')]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for fetching roles
    public function fetch()
    {
        try {

            // Fetching roles
            $roles = Role::with('users')->paginate(10);

            return Response::success(['roles' => $roles]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for assigning role to user
    public function assign_to_user(AssignRoleToUserRequest $request)
    {
        try {

            // Get User
            $user = User::where('email', $request->email)->firstOrFail();

            // Check if the user has assigned the provided role
            if ($user->hasRole($request->role)) {
                return Response::fail([
                    'message' => ResponseMessage::alreadyAssigned($request->role, $user->email),
                    'code' => 409,
                ]);
            }

            // Assigning role to the user
            $user->assignRole($request->role);

            return Response::success(['message' => ResponseMessage::assigned($request->role, $user->email)]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for revoking role for user
    public function revoke_for_user(RevokeRoleForUserRequest $request)
    {
        try {

            // Get User
            $user = User::where('email', $request->email)->firstOrFail();

            // Get Role
            $role = Role::where('name', $request->role)->firstOrFail();

            // Check if the user has assigned the provided role
            if (!$user->hasRole($role->name)) {
                return Response::fail([
                    'message' => ResponseMessage::notAssigned($role->name, $user->email),
                    'code' => 409,
                ]);
            }

            // Revoking role
            $user->removeRole($role->name);

            return Response::success(['message' => ResponseMessage::revoked($role->name, $user->email)]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}