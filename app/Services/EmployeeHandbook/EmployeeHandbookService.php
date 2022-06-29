<?php
namespace App\Services\EmployeeHandbook;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Models\EmployeeHandbook;

class EmployeeHandbookService
{
    // Create Employee Handbook
    public function createEmployeeHandbook($request)
    {
        // Set folder path
        $folderPath = 'employee-handbooks';

        // Upload employee handbook
        $handbook = FileUploadService::upload($request->handbook, $folderPath, 's3');

        // Initiate instance of EmployeeHandbook model
        $employeeHandbook = new EmployeeHandbook();
        $employeeHandbook->detail = $request->detail;
        $employeeHandbook->url = $handbook;
        $employeeHandbook->save();

        // Return success response
        return Response::success([
            'employee-handbook' => $employeeHandbook->with('roles')->latest()->first(),
        ]);
    }

    // Fetch all
    public function fetchEmployeehandbooks()
    {
        // Get Employee handbooks
        $employeeHandbooks = EmployeeHandbook::latest()->paginate(10);

        // Return success response
        return Response::success([
            'employee-handbooks' => $employeeHandbooks,
        ]);
    }

    public function deleteEmployeeHandbook($request)
    {
        // Get employee handbook
        $employeeHandbook = EmployeeHandbook::findOrFail($request->employee_handbook);

        // Delete
        $employeeHandbook->delete();

        // Return Response
        return Response::success([
            'employee-handbook' => $employeeHandbook,
        ]);
    }

    // Fetch single
    public function fetchSingleEmployeeHandbook($request)
    {
        // Get employee handbook
        $employeeHandbook = EmployeeHandbook::where('id', $request->employee_handbook)->firstOrFail();

        // Return success response
        return Response::success([
            'employee-handbook' => $employeeHandbook,
        ]);
    }
}