<?php
namespace App\Services\ItPolicy;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Models\ItPolicy;

class ItPolicyService
{
    public function createItPolicy($request)
    {
        // Set folder path
        $folderPath = 'it-policies';

        // Upload employee handbook
        $itPolicyUrl = FileUploadService::upload($request->it_policy, $folderPath, 's3');

        // Initiate instance of EmployeeHandbook model
        $itPolicy = new ItPolicy();
        $itPolicy->detail = $request->detail;
        $itPolicy->url = $itPolicyUrl;
        $itPolicy->save();

        // Return success response
        return Response::success([
            'it-policy' => $itPolicy->with('roles')->latest()->first(),
        ]);
    }
}