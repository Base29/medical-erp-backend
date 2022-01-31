<?php
namespace App\Services\Department;

use App\Models\Department;
use App\Models\Practice;

/**
 * Department Service
 */

class DepartmentService
{
    // Create Department
    public function createDepartment($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Instance of Department model
        $department = new Department();
        $department->name = $request->name;

        // Save department
        $practice->departments()->save($department);

        // Return success response
        return $department->with('practice')->latest()->first();
    }

    // Fetch departments
    public function fetchDepartments($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get Departments
        $departments = Department::where('practice_id', $practice->id)
            ->with('practice')
            ->latest()
            ->get();

        return $departments;
    }

    // Delete department
    public function deleteDepartment($request)
    {
        // Get department
        $department = Department::findOrFail($request->department);

        // Delete Department
        $department->delete();
    }
}