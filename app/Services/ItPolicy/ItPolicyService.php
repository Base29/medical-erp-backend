<?php
namespace App\Services\ItPolicy;

use App\Helpers\FileUploadService;
use App\Models\ItPolicy;
use Illuminate\Http\Response;

class ItPolicyService
{
    public function createItPolicy($request)
    {
        // Set folder path
        $folderPath = 'it-policies';

        // Upload employee handbook
        $itPolicy = FileUploadService::upload($request->handbook, $folderPath, 's3');

        // Initiate instance of EmployeeHandbook model
        $itPolicy = new ItPolicy();
        $itPolicy->detail = $request->detail;
        $itPolicy->url = $itPolicy;
        $itPolicy->save();

        // Return success response
        return Response::success([
            'it-policy' => $itPolicy->with('roles')->latest()->first(),
        ]);
    }
}