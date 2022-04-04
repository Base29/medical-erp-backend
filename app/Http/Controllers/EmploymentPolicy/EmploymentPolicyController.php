<?php

namespace App\Http\Controllers\EmploymentPolicy;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmploymentPolicy\CreateEmploymentPolicyRequest;
use App\Http\Requests\EmploymentPolicy\DeleteEmploymentPolicyRequest;
use App\Http\Requests\EmploymentPolicy\FetchEmploymentPolicyRequest;
use App\Http\Requests\EmploymentPolicy\UpdateEmploymentPolicyRequest;
use App\Models\EmploymentPolicy;
use App\Services\EmploymentPolicy\EmploymentPolicyService;

class EmploymentPolicyController extends Controller
{

    // Local variable
    protected $employmentPolicyService;

    // Constructor
    public function __construct(EmploymentPolicyService $employmentPolicyService)
    {
        // Inject service
        $this->employmentPolicyService = $employmentPolicyService;
    }

    // Create Employment Policy
    public function create(CreateEmploymentPolicyRequest $request)
    {
        try {

            // Create employment policy
            $employmentPolicy = $this->employmentPolicyService->createEmploymentPolicy($request);

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

            // Update employment policy
            $employmentPolicy = $this->employmentPolicyService->updateEmploymentPolicy($request);

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

    // Delete employment policy
    public function delete(DeleteEmploymentPolicyRequest $request)
    {
        try {

            // Delete employment policy
            $this->employmentPolicyService->deleteEmploymentPolicy($request);

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

            // Fetch user employment policies
            $employmentPolicies = $this->employmentPolicyService->fetchEmploymentPolicies($request);

            // Return success response
            return Response::success([
                'employment-policies' => $employmentPolicies,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}