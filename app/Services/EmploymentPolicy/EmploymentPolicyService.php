<?php
namespace App\Services\EmploymentPolicy;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\EmploymentPolicy;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Storage;

class EmploymentPolicyService
{
    // Create employment policy
    public function createEmploymentPolicy($request)
    {
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

        // Return employment policy
        return $employmentPolicy;
    }

    // Update employment policy
    public function updateEmploymentPolicy($request)
    {
        // Allowed fields
        $allowedFields = [
            'name',
            'sign_date',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new Exception(ResponseMessage::allowedFields($allowedFields), Response::HTTP_BAD_REQUEST);
        }

        // Get employment policy
        $employmentPolicy = EmploymentPolicy::findOrFail($request->employment_policy);

        // Update employment policy
        $employmentPolicyUpdated = UpdateService::updateModel($employmentPolicy, $request->validated(), 'employment_policy');

        if (!$employmentPolicyUpdated) {
            throw new Exception(ResponseMessage::customMessage('Something went wrong. Can not update Employment Policy'), Response::HTTP_BAD_REQUEST);
        }

        // Return success response

        return $employmentPolicy->latest('updated_at')->first();

    }

    // Delete employment policy
    public function deleteEmploymentPolicy($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Assemble user's folder name to be deleted
        $userFolder = 'employment-policies/user-' . $user->id . '/';

        // Delete employment-policies folder of user on S3
        Storage::disk('s3')->deleteDirectory($userFolder);

        // Delete employment policies from DB
        $user->employmentPolicies()->delete();
    }

    // Fetch employment policies
    public function fetchEmploymentPolicies($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        return $user->employmentPolicies;
    }

}