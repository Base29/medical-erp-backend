<?php

namespace App\Http\Controllers\EmployeeHandbook;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeHandbook\CreateEmployeeHandbookRequest;
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

}