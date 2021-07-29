<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class RevokeRoleController extends Controller
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
            ray($validator->errors()->all());
            return response([
                'success' => false,
                'message' => 'All fields are required',
            ], 422);
        }

        // Checking if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response([
                'success' => false,
                'message' => 'User ' . $request->email . ' does not exists',
            ], 400);
        }

        // Checking if role exists
        $role_exists = Role::where('name', $request->role)->first();

        if (!$role_exists) {
            return response([
                'success' => false,
                'message' => 'Role ' . $request->role . ' does not exists.',
            ], 400);
        }

        // Check if the user has assigned the provided role
        if (!$user->hasRole($request->role)) {
            return response([
                'success' => false,
                'message' => 'Role ' . $request->role . ' not assigned to ' . $user->email,
            ], 400);
        }

        // Revoking role
        $user->removeRole($request->role);

        return response([
            'success' => true,
            'message' => 'Role ' . $request->role . ' removed for ' . $user->email,
        ]);
    }
}