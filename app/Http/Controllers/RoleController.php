<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{

    public function create_role(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required',
        ];

        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            ray($validator->errors()->all());
            return response([
                'success' => false,
                'message' => 'All fields are required',
            ], 422);
        }

        // Checking if user have permissions to create roles
        if (!auth()->user()->hasRole('admin')) {
            return response([
                'success' => false,
                'message' => 'You don\'t have authorization to perform this action',
            ]);
        }

        // Check if role is already created
        $role_exists = Role::where('name', $request->name)->first();

        if ($role_exists) {
            return response([
                'success' => false,
                'message' => 'Role already exists',
            ]);
        }

        // Create role
        $role = Role::create(['guard_name' => 'api', 'name' => $request->name]);

        return !$role ? response([
            'success' => false,
            'message' => 'Role not created. Something went wrong.',
        ]) :
        response([
            'success' => true,
            'message' => 'Role created successfully',
        ]);
    }

    public function assign_role(Request $request)
    {

        // Validation rules
        $rules = [
            'email' => 'required|email',
            'role' => 'required',
        ];

        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            ray($validator->errors()->all());
            return response([
                'success' => false,
                'message' => 'All fields are required',
            ], 422);
        }

        // Checking if user have permissions to create roles
        if (!auth()->user()->hasRole('admin')) {
            return response([
                'success' => false,
                'message' => 'You don\'t have authorization to perform this action',
            ]);
        }

        // Checking if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response([
                'success' => false,
                'message' => 'User ' . $request->email . ' does not exists',
            ]);
        }

        // Checking if role exists
        $role_exists = Role::where('name', $request->role)->first();

        if (!$role_exists) {
            return response([
                'success' => false,
                'message' => 'Role ' . $request->role . ' does not exists.',
            ]);
        }

        // Assigning role to the user
        $user->assignRole($request->role);

        return response([
            'success' => true,
            'message' => 'Role ' . $request->role . ' assigned to ' . $user->email,
        ]);
    }

    public function revoke_role(Request $request)
    {
        // Validation rules
        $rules = [
            'email' => 'required|email',
            'role' => 'required',
        ];

        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            ray($validator->errors()->all());
            return response([
                'success' => false,
                'message' => 'All fields are required',
            ], 422);
        }

        // Checking if user have permissions to create roles
        if (!auth()->user()->hasRole('admin')) {
            return response([
                'success' => false,
                'message' => 'You don\'t have authorization to perform this action',
            ]);
        }

        // Checking if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response([
                'success' => false,
                'message' => 'User ' . $request->email . ' does not exists',
            ]);
        }

        // Checking if role exists
        $role_exists = Role::where('name', $request->role)->first();

        if (!$role_exists) {
            return response([
                'success' => false,
                'message' => 'Role ' . $request->role . ' does not exists.',
            ]);
        }

        // Revoking role
        $user->removeRole($request->role);

        return response([
            'success' => true,
            'message' => 'Role ' . $request->role . ' removed for ' . $user->email,
        ]);
    }
}