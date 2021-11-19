<?php

namespace App\Http\Controllers\ContractSummary;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContractSummary\CreateContractSummaryRequest;
use App\Http\Requests\ContractSummary\UpdateContractSummaryRequest;
use App\Models\ContractSummary;
use App\Models\User;

class ContractSummaryController extends Controller
{
    // Create contract summary
    public function create(CreateContractSummaryRequest $request)
    {
        try {

            // Fetch user
            $user = User::findOrFail($request->user);

            // Upload contract
            $url = FileUploadService::upload(
                $request->file('contract_document'),
                'employeeContracts',
                's3');

            // Create contract summary
            $contractSummary = new ContractSummary();
            $contractSummary->employee_type = $request->employee_type;
            $contractSummary->employee_start_date = $request->employee_start_date;
            $contractSummary->contract_start_date = $request->contract_start_date;
            $contractSummary->working_time_pattern = $request->working_time_pattern;
            $contractSummary->contracted_hours_per_week = $request->contracted_hours_per_week;
            $contractSummary->min_leave_entitlement = $request->min_leave_entitlement;
            $contractSummary->contract_document = $url;
            $user->contractSummary()->save($contractSummary);

            // Return created contract summary
            return Response::success([
                'contract_summary' => $contractSummary->with('user')->latest()->first(),
            ]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update contract summary
    public function update(UpdateContractSummaryRequest $request)
    {
        try {
            // Allowed fields that can be updated
            $allowedFields = [
                'employee_type',
                'employee_start_date',
                'contract_start_date',
                'working_time_pattern',
                'contracted_hours_per_week',
                'contract_document',
                'min_leave_entitlement',
            ];

            // Checking if the $request doesn't contain any of the allowed fields
            if (!$request->hasAny($allowedFields)) {
                return Response::fail([
                    'message' => ResponseMessage::allowedFields($allowedFields),
                    'code' => 400,
                ]);
            }

            // Check if the contract summary exists
            $contractSummary = ContractSummary::findOrFail($request->contract_summary);

            if (!$contractSummary) {
                return Response::fail([
                    'code' => 404,
                    'message' => ResponseMessage::notFound(
                        'Contract Summary',
                        $request->contract_summary,
                        false
                    ),
                ]);
            }

            // Update contract summary
            $contractSummaryUpdated = UpdateService::updateModel(
                $contractSummary,
                $request->all(),
                'contract_summary'
            );

            if ($contractSummaryUpdated) {
                return Response::success([
                    'contract_summary' => $contractSummary->with('user')->latest('updated_at')->first(),
                ]);
            }

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}