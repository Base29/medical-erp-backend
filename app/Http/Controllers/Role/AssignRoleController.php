<?php

namespace App\Http\Controllers\Role;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AssignRoleController extends Controller
{
    public function __invoke(Request $request)
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
            // Return error messages against $rules
            return CustomValidation::error_messages($rules, $validator);
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
}