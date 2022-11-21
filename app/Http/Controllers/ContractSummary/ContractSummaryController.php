<?php

namespace App\Http\Controllers\ContractSummary;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContractSummary\CreateContractSummaryRequest;
use App\Http\Requests\ContractSummary\FetchSingleContractSummaryRequest;
use App\Http\Requests\ContractSummary\UpdateContractSummaryRequest;
use App\Services\ContractSummary\ContractSummaryService;
use Exception;

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
                'code' => Response::HTTP_CREATED,
                'contract_summary' => $contractSummary,
            ]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
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
                'code' => Response::HTTP_OK,
                'contract-summary' => $contractSummary,
            ]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
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
                'code' => Response::HTTP_OK,
                'contract_summary' => $contractSummary,
            ]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
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
                'code' => Response::HTTP_OK,
                'message' => ResponseMessage::deleteSuccess('Contract Summary'),
            ]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Sign contract summary
    public function sign()
    {
        try {
            // Logic here
            return $this->contractSummaryService->signContractSummary();

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}