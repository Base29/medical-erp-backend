<?php

namespace App\Http\Controllers\Role;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\AssignRoleToUserRequest;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\RevokeRoleForUserRequest;
use App\Services\Role\RoleService;
use Exception;

class RoleController extends Controller
{
    // Local variable
    protected $roleService;

    // Constructor
    public function __construct(RoleService $roleService)
    {
        // Inject Service
        $this->roleService = $roleService;
    }

    // Method for create role
    public function create(CreateRoleRequest $request)
    {

        try {

            // Create role service
            return $this->roleService->createRole($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for deleting role
    public function delete($id)
    {
        try {

            // Delete role
            return $this->roleService->deleteRole($id);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for fetching roles
    public function fetch()
    {
        try {

            // Fetch roles
            return $this->roleService->fetchRoles();

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for assigning role to user
    public function assignToUser(AssignRoleToUserRequest $request)
    {
        try {

            // Assign role to user
            return $this->roleService->assignRoleToUser($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for revoking role for user
    public function revokeForUser(RevokeRoleForUserRequest $request)
    {
        try {

            // Revoke role for user
            return $this->roleService->revokeRoleForUser($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}