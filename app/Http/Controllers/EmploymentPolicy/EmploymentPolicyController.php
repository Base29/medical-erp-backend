<?php

namespace App\Http\Controllers\EmploymentPolicy;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmploymentPolicy\CreateEmploymentPolicyRequest;
use App\Http\Requests\EmploymentPolicy\DeleteEmploymentPolicyRequest;
use App\Http\Requests\EmploymentPolicy\FetchEmploymentPolicyRequest;
use App\Http\Requests\EmploymentPolicy\UpdateEmploymentPolicyRequest;
use App\Models\EmploymentPolicy;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

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

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update employment
    public function update(UpdateEmploymentPolicyRequest $request)
    {
        try {

            // Allowed fields
            $allowedFields = [
                'name',
                'sign_date',
            ];

            // Checking if the $request doesn't contain any of the allowed fields
            if (!$request->hasAny($allowedFields)) {
                return Response::fail([
                    'message' => ResponseMessage::allowedFields($allowedFields),
                    'code' => 400,
                ]);
            }

            // Get employment policy
            $employmentPolicy = EmploymentPolicy::findOrFail($request->employment_policy);

            // Update employment policy
            $employmentPolicyUpdated = UpdateService::updateModel($employmentPolicy, $request->all(), 'employment_policy');

            if (!$employmentPolicyUpdated) {
                return Response::fail([
                    'code' => 400,
                    'message' => ResponseMessage::customMessage('Something went wrong. Can not update Employment Policy'),
                ]);
            }

            // Return success response
            return Response::success([
                'employment-policy' => $employmentPolicy->latest('updated_at')->first(),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete employment policy
    public function delete(DeleteEmploymentPolicyRequest $request)
    {
        try {

            // Get user
            $user = User::findOrFail($request->user);

            // Assemble user's folder name to be deleted
            $userFolder = 'employment-policies/user-' . $user->id . '/';

            // Delete employment-policies folder of user on S3
            Storage::disk('s3')->deleteDirectory($userFolder);

            // Delete employment policies from DB
            $user->employmentPolicies()->delete();

            // Return success response
            return Response::success([
                'message' => ResponseMessage::deleteSuccess('Employment Policies'),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch employment policies for user
    public function fetch(FetchEmploymentPolicyRequest $request)
    {
        try {

            // Get user
            $user = User::findOrFail($request->user);

            return Response::success([
                'employment-policies' => $user->employmentPolicies,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
