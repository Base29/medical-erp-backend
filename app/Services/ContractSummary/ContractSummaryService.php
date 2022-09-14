<?php
namespace App\Services\ContractSummary;

/**
 * Contract Summary Service
 */

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\ContractSummary;
use App\Models\User;

class ContractSummaryService
{
    // Create contract summary
    public function createContractSummary($request)
    {
        // Fetch user
        $user = User::findOrFail($request->user);

        // Initiating a null variable $url for the contract_document
        $url = null;
        if ($request->hasFile('contract_document')) {
            // Upload contract
            $url = FileUploadService::upload(
                $request->file('contract_document'),
                'employeeContracts',
                's3');
        }

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

        // Attach work pattern with user
        $user->workPatterns()->attach($contractSummary->working_time_pattern);

        // Return created contract summary
        return $contractSummary->with('user.profile')->latest()->first();
    }

    // Update contract summary
    public function updateContractSummary($request)
    {
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
            throw new \Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Check if the contract summary exists
        $contractSummary = ContractSummary::findOrFail($request->contract_summary);

        if (!$contractSummary) {
            throw new \Exception(ResponseMessage::notFound(
                'Contract Summary',
                $request->contract_summary,
                false
            ));
        }

        // Update contract summary
        UpdateService::updateModel(
            $contractSummary,
            $request->validated(),
            'contract_summary'
        );

        // Return updated contract summary
        return $contractSummary->with('user.profile')->latest('updated_at')->first();
    }

    // Fetch single contract summary
    public function fetchSingleContractSummary($request)
    {
        // Return single contract summary
        return ContractSummary::where('id', $request->contract_summary)->with('user.profile')->first();
    }

    // Delete contract summary
    public function deleteContractSummary($id)
    {
        // Fetch contract summary
        $contractSummary = ContractSummary::findOrFail($id);

        if (!$contractSummary) {
            throw new \Exception(ResponseMessage::notFound('Contract Summary', $id, false));
        }

        $contractSummary->delete();
    }

    // Sign contract summary
    public function signContractSummary()
    {
        // Get authenticated user
        $authenticatedUser = auth()->user();

        // Get contract summary of the $authenticatedUser
        $contractSummary = ContractSummary::where('user_id', $authenticatedUser->id)
            ->firstOrFail();

        // Check if $contractSummary is already signed
        if ($contractSummary->is_signed) {
            throw new \Exception(ResponseMessage::customMessage('Contract summary ' . $contractSummary->id . ' already signed'));
        }

        // Update the is_signed column
        $contractSummary->is_signed = 1;
        $contractSummary->save();

        // Return success response
        return Response::success([
            'user' => $authenticatedUser,
        ]);
    }
}