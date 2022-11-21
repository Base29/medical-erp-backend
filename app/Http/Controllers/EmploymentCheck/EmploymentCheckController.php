<?php

namespace App\Http\Controllers\EmploymentCheck;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmploymentCheck\CreateEmploymentCheckRequest;
use App\Http\Requests\EmploymentCheck\DeleteEmploymentCheckRequest;
use App\Http\Requests\EmploymentCheck\FetchSingleEmploymentCheckRequest;
use App\Http\Requests\EmploymentCheck\UpdateEmploymentCheckRequest;
use App\Services\EmploymentCheck\EmploymentCheckService;
use Exception;

class EmploymentCheckController extends Controller
{

    // Local variable
    protected $employmentCheckService;

    // Constructor
    public function __construct(EmploymentCheckService $employmentCheckService)
    {
        // Inject service
        $this->employmentCheckService = $employmentCheckService;
    }

    // Create employment check
    public function create(CreateEmploymentCheckRequest $request)
    {
        try {

            // Create employment check
            $employmentCheck = $this->employmentCheckService->createEmploymentCheck($request);

            // Return response
            return Response::success([
                'code' => Response::HTTP_CREATED,
                'employment_check' => $employmentCheck,
            ]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update employment check
    public function update(UpdateEmploymentCheckRequest $request)
    {
        try {

            // Update employment check
            $employmentCheck = $this->employmentCheckService->updateEmploymentCheck($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'employment_check' => $employmentCheck->latest('updated_at')->first(),
            ]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete employment check
    public function delete(DeleteEmploymentCheckRequest $request)
    {
        try {

            // Delete employment check
            $this->employmentCheckService->deleteEmploymentCheck($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'message' => ResponseMessage::deleteSuccess('Employment Check'),
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Get single employment check
    public function fetchSingle(FetchSingleEmploymentCheckRequest $request)
    {
        try {

            // Fetch single employment check
            $employmentCheck = $this->employmentCheckService->fetchSingleEmploymentCheck($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'employment-check' => $employmentCheck,
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}