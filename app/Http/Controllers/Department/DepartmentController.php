<?php

namespace App\Http\Controllers\Department;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Department\CreateDepartmentRequest;
use App\Http\Requests\Department\DeleteDepartmentRequest;
use App\Http\Requests\Department\FetchDepartmentRequest;
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

    // Fetch department by practice
    public function fetch(FetchDepartmentRequest $request)
    {
        try {
            // Get practice
            $practice = Practice::findOrFail($request->practice);

            // Get Departments
            $departments = Department::where('practice_id', $practice->id)
                ->latest()
                ->paginate(10);

            // Return success response
            return Response::success([
                'departments' => $departments,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete department
    public function delete(DeleteDepartmentRequest $request)
    {
        try {
            // Get department
            $department = Department::findOrFail($request->department);

            // Delete Department
            $department->delete();

            // Return success response
            return Response::success([
                'message' => ResponseMessage::deleteSuccess('Department'),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}