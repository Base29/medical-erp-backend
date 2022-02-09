<?php
namespace App\Services\Department;

use App\Helpers\Response;
use App\Models\Department;
use App\Models\HiringRequest;
use App\Models\Practice;
use App\Models\User;

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

        // Get user for department head
        $departmentHead = User::findOrFail($request->department_head);

        // Instance of Department model
        $department = new Department();
        $department->name = $request->name;
        $department->user_id = $departmentHead->id;

        // Save department
        $practice->departments()->save($department);

        // Return success response
        return $department->with('practice', 'users')->latest()->first();
    }

    // Fetch departments
    public function fetchDepartments($request)
    {
        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get Departments
        $departments = Department::where('practice_id', $practice->id)
            ->with('practice', 'departmentHead', 'users.profile', 'users.positionSummary')
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

    // Assign user department
    public function assignUserToDepartment($request)
    {
        // Get department
        $department = Department::findOrFail($request->department);

        // Get user
        $user = User::findOrFail($request->user);

        // Add $department->id to $user
        $user->department_id = $department->id;
        $user->save();

        // Return success response
        return Response::success([
            'user' => $user->with('profile.hiringRequest', 'positionSummary', 'contractSummary', 'roles', 'practices', 'department')
                ->latest('updated_at')
                ->first(),
        ]);
    }

    // Fetch single department
    public function fetchSingleDepartment($request)
    {
        // Get department
        $department = Department::where('id', $request->department)
            ->with('practice', 'departmentHead.profile', 'users.profile', 'users.positionSummary')
            ->firstOrFail();

        $result = $department->toArray();

        // Getting count of permanent contract
        $permanent = $this->processCount($department->id, 'contract_type', 'permanent');

        // Getting count of fixed term contract
        $fixedTerm = $this->processCount($department->id, 'contract_type', 'fixed-term');

        // Getting count of casual contract
        $casual = $this->processCount($department->id, 'contract_type', 'casual');

        // Getting count of zero hour contract
        $zeroHour = $this->processCount($department->id, 'contract_type', 'zero-hour', $request);

        $result['count']['permanent'] = $permanent;
        $result['count']['fixed-term'] = $fixedTerm;
        $result['count']['casual'] = $casual;
        $result['count']['zero-hour'] = $zeroHour;

        // Return success response
        return Response::success([
            'department' => $result,
        ]);
    }

    // Process count
    private function processCount($departmentId = null, $column, $value)
    {

        return HiringRequest::where(['department_id' => $departmentId, $column => $value])->count();

    }
}