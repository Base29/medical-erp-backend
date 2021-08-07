<?php

namespace App\Http\Controllers\Role;

use App\Helpers\CustomValidation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class CreateRoleController extends Controller
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
}