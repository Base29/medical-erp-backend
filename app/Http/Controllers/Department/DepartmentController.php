<?php

namespace App\Http\Controllers\Department;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Department\CreateDepartmentRequest;
use App\Models\Department;
use App\Models\Practice;

class DepartmentController extends Controller
{
    // Create department
    public function create(CreateDepartmentRequest $request)
    {
        try {
            // Get practice
            $practice = Practice::findOrFail($request->practice);

            // Instance of Department model
            $department = new Department();
            $department->name = $request->name;

            // Save department
            $practice->departments()->save($department);

            // Return success response
            return Response::success([
                'department' => $department->with('practice')->latest()->first(),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}