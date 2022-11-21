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
use App\Services\Permission\PermissionService;
use Exception;

class PermissionController extends Controller
{
    // Local variable
    protected $permissionService;

    // Constructor
    public function __construct(PermissionService $permissionService)
    {
        // Inject service
        $this->permissionService = $permissionService;
    }

    // Method for creating a permission
    public function create(CreatePermissionRequest $request)
    {
        try {

            // Create permission
            $permission = $this->permissionService->createPermission($request);

            // Return success response
            return Response::success(['permission' => $permission]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for deleting permission
    public function delete($id)
    {
        try {

            // Delete permission
            $this->permissionService->deletePermission($id);

            return Response::success(['message' => ResponseMessage::deleteSuccess('Permission')]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }

    }

    // Method for fetching permissions
    public function fetch()
    {

        try {

            // Fetching permissions
            $permissions = $this->permissionService->fetchPermissions();

            // Return success response
            return Response::success(['permissions' => $permissions]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for assigning permission to a role
    public function assignToRole(AssignPermissionToRoleRequest $request)
    {
        try {

            // Assign to role
            return $this->permissionService->assignPermissionToRole($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for assigning permission to a user
    public function assignToUser(AssignPermissionToUserRequest $request)
    {
        try {

            // Assign permission to user
            return $this->permissionService->assignPermissionToUser($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }

    }

    // Method for revoking permission for role
    public function revokeForRole(RevokePermissionForRoleRequest $request)
    {

        try {

            // Revoke permission for role
            return $this->permissionService->revokePermissionForRole($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }

    }

    // Method for revoking permission for user
    public function revokeForUser(RevokePermissionForUserRequest $request)
    {

        try {
            // Revoke permission for user
            return $this->permissionService->revokePermissionForUser($request);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}