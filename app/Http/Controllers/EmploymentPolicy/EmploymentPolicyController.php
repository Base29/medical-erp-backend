<?php

namespace App\Http\Controllers\EmploymentPolicy;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmploymentPolicy\CreateEmploymentPolicyRequest;
use App\Models\EmploymentPolicy;
use App\Models\User;

class EmploymentPolicyController extends Controller
{
    // Create Employment Policy
    public function create(CreateEmploymentPolicyRequest $request)
    {
        try {

            // Get user
            $user = User::findOrFail($request->user);

            // Initiate empty variable $url
            $url = null;
            // Request has file
            if ($request->hasFile('attachment')) {
                $folderName = 'employment-policies/user-' . $user->id;
                $url = FileUploadService::upload($request->file('attachment'), $folderName, 's3');
            }
            // Create instance of EmploymentPolicy
            $employmentPolicy = new EmploymentPolicy();
            $employmentPolicy->name = $request->name;
            $employmentPolicy->attachment = $url;
            $employmentPolicy->sign_date = $request->sign_date;

            // Save employment policy for user
            $user->employmentPolicies()->save($employmentPolicy);

            // Return success response
            return Response::success([
                'employment-policy' => $employmentPolicy,
            ]);

        } catch (\Exception$e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}