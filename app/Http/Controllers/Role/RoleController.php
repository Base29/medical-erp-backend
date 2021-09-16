<?php

namespace App\Http\Controllers\Role;

use App\Helpers\CustomValidation;
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
            return response([
                'success' => false,
                'message' => $request->name . ' role already exists',
            ], 409);
        }

        // Create role
        $role = Role::create(['guard_name' => 'api', 'name' => $request->name]);

        return !$role ? response([
            'success' => false,
            'message' => 'Role not created. Something went wrong.',
        ], 400) :
        response([
            'success' => true,
            'message' => $role->name . ' role created successfully',
        ], 200);
    }

    // Method for deleting role
    public function delete($id)
    {
        // Check if the user exists with the provided $id
        $role = Role::find($id);

        if (!$role) {
            return response([
                'success' => false,
                'message' => 'No role found with the provided id ' . $id,
            ], 404);
        }

        // Delete user with the provided $id
        $role->delete();
        return response([
            'success' => true,
            'message' => 'Role delete successfully',
        ], 200);
    }

    // Method for fetching roles
    public function fetch()
    {
        // Fetching roles
        $roles = Role::with('users')->paginate(10);
        return response([
            'success' => true,
            'roles' => $roles,
        ], 200);
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
            return response([
                'success' => false,
                'message' => 'User with email ' . $request->email . ' does not exists',
            ], 404);
        }

        // Checking if role exists
        $role_exists = Role::where('name', $request->role)->first();

        if (!$role_exists) {
            return response([
                'success' => false,
                'message' => 'Role ' . $request->role . ' does not exists.',
            ], 404);
        }

        // Check if the user has assigned the provided role
        if ($user->hasRole($request->role)) {
            return response([
                'success' => false,
                'message' => 'Role ' . $request->role . ' already assigned to ' . $user->email,
            ], 409);
        }

        // Assigning role to the user
        $user->assignRole($request->role);

        return response([
            'success' => true,
            'message' => 'Role ' . $request->role . ' assigned to ' . $user->email,
        ], 200);
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
            return response([
                'success' => false,
                'message' => 'User ' . $request->email . ' does not exists',
            ], 404);
        }

        // Checking if role exists
        $role_exists = Role::where('name', $request->role)->first();

        if (!$role_exists) {
            return response([
                'success' => false,
                'message' => 'Role ' . $request->role . ' does not exists.',
            ], 404);
        }

        // Check if the user has assigned the provided role
        if (!$user->hasRole($request->role)) {
            return response([
                'success' => false,
                'message' => 'Role ' . $request->role . ' not assigned to ' . $user->email,
            ], 409);
        }

        // Revoking role
        $user->removeRole($request->role);

        return response([
            'success' => true,
            'message' => 'Role ' . $request->role . ' removed for ' . $user->email,
        ], 200);
    }
}