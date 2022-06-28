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
}