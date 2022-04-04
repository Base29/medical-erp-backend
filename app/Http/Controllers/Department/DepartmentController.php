<?php

namespace App\Http\Controllers\Department;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Department\AssignUserToDepartmentRequest;
use App\Http\Requests\Department\CreateDepartmentRequest;
use App\Http\Requests\Department\DeleteDepartmentRequest;
use App\Http\Requests\Department\FetchDepartmentRequest;
use App\Http\Requests\Department\FetchSingleDepartmentRequest;
use App\Models\Department;
use App\Services\Department\DepartmentService;

class DepartmentController extends Controller
{
    // Local variable
    protected $departmentService;

    // Constructor
    public function __construct(DepartmentService $departmentService)
    {
        // Inject service
        $this->departmentService = $departmentService;
    }

    // Create department
    public function create(CreateDepartmentRequest $request)
    {
        try {

            // Create department service
            $department = $this->departmentService->createDepartment($request);

            // Return success response
            return Response::success([
                'department' => $department,
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
            // Fetch departments service
            $departments = $this->departmentService->fetchDepartments($request);

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

            // Delete department service
            $this->departmentService->deleteDepartment($request);

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

    // Assign user to department
    public function assignUser(AssignUserToDepartmentRequest $request)
    {
        try {
            // Assign user to practice
            return $this->departmentService->assignUserToDepartment($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single department
    public function fetchSingle(FetchSingleDepartmentRequest $request)
    {
        try {
            // Fetch single department
            return $this->departmentService->fetchSingleDepartment($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}