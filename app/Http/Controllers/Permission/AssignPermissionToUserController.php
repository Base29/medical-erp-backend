<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class AssignPermissionToUserController extends Controller
{
    public function __invoke(Request $request)
    {
        ray($request->all());

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

        // Check if the user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response([
                'success' => false,
                'message' => 'User doesn\'t exist with the provided email ' . $request->email,
            ]);
        }

        // Check if the user already has the provided permission
        $already_has_permission = $user->hasPermissionTo($request->permission);

        if ($already_has_permission) {
            return response([
                'success' => false,
                'message' => 'User ' . $user->email . ' already has ' . $request->permission . ' permission',
            ]);
        }

        // Check if the permission exists
        $permission = Permission::where('name', $request->permission)->first();

        if (!$permission) {
            return response([
                'success' => false,
                'message' => 'Permission ' . $request->permission . ' doesn\'t exist',
            ]);
        }

        // Assigning permission to the user
        $user->givePermissionTo($request->permission);

        return response([
            'success' => true,
            'message' => 'Permission ' . $permission->name . ' assigned to ' . $user->email,
        ]);

    }
}