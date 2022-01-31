<?php

namespace App\Http\Controllers\ContractSummary;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContractSummary\CreateContractSummaryRequest;
use App\Http\Requests\ContractSummary\FetchSingleContractSummaryRequest;
use App\Http\Requests\ContractSummary\UpdateContractSummaryRequest;
use App\Models\ContractSummary;
use App\Services\ContractSummary\ContractSummaryService;

class ContractSummaryController extends Controller
{

    // Local variable
    protected $contractSummaryService;

    // Constructor
    public function __construct(ContractSummaryService $contractSummaryService)
    {
        // Inject service
        $this->contractSummaryService = $contractSummaryService;
    }

    // Create contract summary
    public function create(CreateContractSummaryRequest $request)
    {
        try {

            // Create contact summary service
            $contractSummary = $this->contractSummaryService->createContractSummary($request);

            // Return created contract summary
            return Response::success([
                'contract_summary' => $contractSummary,
            ]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update contract summary
    public function update(UpdateContractSummaryRequest $request)
    {
        try {
            // Update contract summary service
            $contractSummary = $this->contractSummaryService->updateContractSummary($request);

            // Return success response
            return Response::success([
                'contract-summary' => $contractSummary,
            ]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single contract summary
    public function fetchSingle(FetchSingleContractSummaryRequest $request)
    {
        try {

            // Fetch single contract summary
            $contractSummary = $this->contractSummaryService->fetchSingleContractSummary($request);

            // Return response with the Contract Summary
            return Response::success([
                'contract_summary' => $contractSummary,
            ]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete contract summary
    public function delete($id)
    {
        try {

            // Delete contract summary service
            $this->contractSummaryService->deleteContractSummary($id);

            return Response::success([
                'message' => ResponseMessage::deleteSuccess('Contract Summary'),
            ]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}