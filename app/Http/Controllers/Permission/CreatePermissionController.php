<?php

namespace App\Http\Controllers\Permission;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
            // Return error messages against $rules
            return CustomValidation::error_messages($rules, $validator);
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