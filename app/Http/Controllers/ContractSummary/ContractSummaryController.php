<?php

namespace App\Http\Controllers\ContractSummary;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContractSummary\CreateContractSummaryRequest;
use App\Models\ContractSummary;
use App\Models\User;
use Illuminate\Support\Carbon;

class ContractSummaryController extends Controller
{
    // Create contract summary
    public function create(CreateContractSummaryRequest $request)
    {
        try {

            // Fetch user
            $user = User::findOrFail($request->user);

            // Upload contract
            $url = FileUploadService::upload($request->file('contract_document'), 'employeeContracts', 's3');

            // Create contract summary
            $contractSummary = new ContractSummary();
            $contractSummary->employee_type = $request->employee_type;
            $contractSummary->employee_start_date = Carbon::parse($request->employee_start_date)->format('Y-m-d');
            $contractSummary->contract_start_date = Carbon::parse($request->contract_start_date)->format('Y-m-d');
            $contractSummary->working_time_pattern = $request->working_time_pattern;
            $contractSummary->contracted_hours_per_week = $request->contracted_hours_per_week;
            $contractSummary->min_leave_entitlement = $request->min_leave_entitlement;
            $contractSummary->contract_document = $url;
            $user->contractSummary()->save($contractSummary);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}