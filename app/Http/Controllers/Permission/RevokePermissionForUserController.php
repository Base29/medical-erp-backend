<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class RevokePermissionForUserController extends Controller
{
    public function __invoke(Request $request)
    {
        // Validation rules
        $rules = [
            'email' => 'required|email',
            'permission' => 'required',
        ];

        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            $errors = $validator->errors();
            if (Arr::has($errors->messages(), 'email')) {
                return response([
                    'success' => false,
                    'message' => $errors->messages()['email'][0],
                ], 422);
            } else if (Arr::has($errors->messages(), 'permission')) {
                return response([
                    'success' => false,
                    'message' => $errors->messages()['permission'][0],
                ], 422);
            }
        }

        // Check if the user exists for which the permission is being revoked
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response([
                'success' => false,
                'message' => 'User ' . $request->email . ' doesn\'t exists',
            ], 404);
        }

        // Check if the permission exists which is being revoked for the user
        $permission = Permission::where('name', $request->permission)->first();

        if (!$permission) {
            return response([
                'success' => false,
                'message' => 'Permission ' . $request->permission . ' doesn\'t exist',
            ], 404);
        }

        // Check if the user has the permission that is being revoked
        $user_has_permission = $user->hasPermissionTo($permission->name);

        if (!$user_has_permission) {
            return response([
                'success' => false,
                'message' => 'User ' . $user->email . ' doesn\'t have ' . $permission->name . ' permission',
            ], 400);
        }

        // Revoke permission for the provided user
        $user->revokePermissionTo($permission->name);

        return response([
            'success' => true,
            'message' => 'Permission ' . $permission->name . ' revoked for user ' . $user->email,
        ], 200);
    }
}