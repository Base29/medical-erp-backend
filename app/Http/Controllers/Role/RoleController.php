<?php

namespace App\Http\Controllers\Role;

use App\Helpers\CustomValidation;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    // Method for create role
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

        // Check if role is already created
        $role_exists = Role::where('name', $request->name)->first();

        if ($role_exists) {
            return Response::fail([
                'message' => ResponseMessage::alreadyExists($request->name),
                'code' => 409,
            ]);
        }

        // Create role
        $role = Role::create(['guard_name' => 'api', 'name' => $request->name]);

        return Response::success(['role' => $role->name]);
    }

    // Method for deleting role
    public function delete($id)
    {
        // Check if the user exists with the provided $id
        $role = Role::find($id);

        if (!$role) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Role', $id, false),
                'code' => 404,
            ]);
        }

        // Delete user with the provided $id
        $role->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('Role')]);
    }

    // Method for fetching roles
    public function fetch()
    {
        // Fetching roles
        $roles = Role::with('users')->paginate(10);

        return Response::success(['roles' => $roles]);
    }

    // Method for assigning role to user
    public function assign_to_user(Request $request)
    {
        // Validation rules
        $rules = [
            'email' => 'required|email',
            'role' => 'required',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Checking if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return Response::fail([
                'message' => ResponseMessage::notFound('User', $request->email, true),
                'code' => 404,
            ]);
        }

        // Checking if role exists
        $role_exists = Role::where('name', $request->role)->first();

        if (!$role_exists) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Role', $request->role, false),
                'code' => 404,
            ]);
        }

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
    }

    // Method for revoking role for user
    public function revoke_for_user(Request $request)
    {
        // Validation rules
        $rules = [
            'email' => 'required|email',
            'role' => 'required',
        ];

        // Validation errors
        $request_errors = CustomValidation::validate_request($rules, $request);

        // Return errors
        if ($request_errors) {
            return $request_errors;
        }

        // Checking if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return Response::fail([
                'message' => ResponseMessage::notFound('User', $request->email, true),
                'code' => 404,
            ]);
        }

        // Checking if role exists
        $role = Role::where('name', $request->role)->first();

        if (!$role) {
            return Response::fail([
                'message' => ResponseMessage::notFound('Role', $request->role, false),
                'code' => 404,
            ]);
        }

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
    }
}