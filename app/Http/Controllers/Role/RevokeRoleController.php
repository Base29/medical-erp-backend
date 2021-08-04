<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
            $errors = $validator->errors();

            // Return error messages for role
            if (Arr::has($errors->messages(), 'role')) {
                return response([
                    'success' => false,
                    'message' => $errors->messages()['role'][0],
                ], 422);
            }

            // Return error messages for email
            if (Arr::has($errors->messages(), 'email')) {
                return response([
                    'success' => false,
                    'message' => $errors->messages()['email'][0],
                ], 422);
            }
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