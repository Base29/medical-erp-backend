<?php

namespace App\Http\Controllers\EmployeeHandbook;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeHandbook\CreateEmployeeHandbookRequest;
use App\Http\Requests\EmployeeHandbook\DeleteEmployeeHandbookRequest;
use App\Http\Requests\EmployeeHandbook\FetchSingleEmployeeHandbookRequest;
use App\Services\EmployeeHandbook\EmployeeHandbookService;

class EmployeeHandbookController extends Controller
{
    // Local variable
    protected $employeeHandbookService;

    // Constructor
    public function __construct(EmployeeHandbookService $employeeHandbookService)
    {
        // Inject Service
        $this->employeeHandbookService = $employeeHandbookService;
    }

    // Create
    public function create(CreateEmployeeHandbookRequest $request)
    {
        try {
            // Employee Handbook service
            return $this->employeeHandbookService->createEmployeeHandbook($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch All
    public function fetch()
    {
        try {
            // Logic here
            return $this->employeeHandbookService->fetchEmployeehandbooks();

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete
    public function delete(DeleteEmployeeHandbookRequest $request)
    {
        try {
            // Logic here
            return $this->employeeHandbookService->deleteEmployeeHandbook($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single employee handbook
    public function fetchSingle(FetchSingleEmployeeHandbookRequest $request)
    {
        try {
            // Logic here
            return $this->employeeHandbookService->fetchSingleEmployeeHandbook($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

}