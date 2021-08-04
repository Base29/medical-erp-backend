<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class CreatePermissionController extends Controller
{
    public function __invoke(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required',
        ];

        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            $errors = $validator->errors();
            // Return error messages for email
            if (Arr::has($errors->messages(), 'name')) {
                return response([
                    'success' => false,
                    'message' => $errors->messages()['name'][0],
                ], 422);
            }
        }

        // Check if the permission already exists
        $permission_exists = Permission::where('name', $request->name)->first();

        if ($permission_exists) {
            return response([
                'success' => false,
                'message' => 'Permission ' . $request->name . ' already exists',
            ], 409);
        }

        // Create permission
        $permission = Permission::create(['guard_name' => 'api', 'name' => $request->name]);

        return !$permission ? response([
            'success' => false,
            'message' => 'Role not created. Something went wrong.',
        ], 400) :
        response([
            'success' => true,
            'message' => $permission->name . ' permission created successfully',
        ], 200);
    }
}