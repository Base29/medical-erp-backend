<?php

namespace App\Http\Controllers\EmploymentHistory;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmploymentHistory\CreateEmploymentHistoryRequest;
use App\Http\Requests\EmploymentHistory\DeleteEmploymentHistoryRequest;
use App\Http\Requests\EmploymentHistory\FetchEmploymentHistoryRequest;
use App\Http\Requests\EmploymentHistory\FetchSingleEmploymentHistoryRequest;
use App\Http\Requests\EmploymentHistory\UpdateEmploymentHistoryRequest;
use App\Services\EmploymentHistory\EmploymentHistoryService;
use Exception;

class EmploymentHistoryController extends Controller
{

    // Local variable
    protected $employmentHistoryService;

    // Constructor
    public function __construct(EmploymentHistoryService $employmentHistoryService)
    {
        // Inject service
        $this->employmentHistoryService = $employmentHistoryService;
    }

    // Create employment history
    public function create(CreateEmploymentHistoryRequest $request)
    {
        try {

            // Create employment history
            $employmentHistory = $this->employmentHistoryService->createEmploymentHistory($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_CREATED,
                'employment-history' => $employmentHistory,
            ]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update employment history
    public function update(UpdateEmploymentHistoryRequest $request)
    {
        try {

            // Update employment history
            $employmentHistory = $this->employmentHistoryService->updateEmploymentHistory($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'employment-history' => $employmentHistory,
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete user employment history
    public function delete(DeleteEmploymentHistoryRequest $request)
    {
        try {

            // Delete employment history
            $this->employmentHistoryService->deleteEmploymentHistory($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'message' => ResponseMessage::deleteSuccess('Employment History'),
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch all employment experience for a user
    public function fetch(FetchEmploymentHistoryRequest $request)
    {
        try {

            // Fetch employment history
            $employmentHistories = $this->employmentHistoryService->fetchEmploymentHistory($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'employment-histories' => $employmentHistories,
            ]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single employment history
    public function fetchSingle(FetchSingleEmploymentHistoryRequest $request)
    {
        try {

            // Fetch single employment history
            $employmentHistory = $this->employmentHistoryService->fetchSingleEmploymentHistory($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'employment-history' => $employmentHistory,
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}