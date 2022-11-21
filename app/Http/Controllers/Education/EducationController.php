<?php

namespace App\Http\Controllers\Education;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Education\CreateEducationRequest;
use App\Http\Requests\Education\DeleteEducationRequest;
use App\Http\Requests\Education\FetchEducationRequest;
use App\Http\Requests\Education\UpdateEducationRequest;
use App\Services\Education\EducationService;
use Exception;

class EducationController extends Controller
{
    // Local variable
    protected $educationService;

    // Constructor
    public function __construct(EducationService $educationService)
    {
        // Inject service
        $this->educationService = $educationService;
    }

    // Create Education
    public function create(CreateEducationRequest $request)
    {
        try {

            // Create education service
            $education = $this->educationService->createEducation($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_CREATED,
                'education' => $education,
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch user's education
    public function fetch(FetchEducationRequest $request)
    {
        try {

            // Fetch education service
            $education = $this->educationService->fetchEducation($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'education' => $education,
            ]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete education
    public function delete(DeleteEducationRequest $request)
    {
        try {

            // Delete education service
            $this->educationService->deleteEducation($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'message' => ResponseMessage::deleteSuccess('Education'),
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update education
    public function update(UpdateEducationRequest $request)
    {
        try {

            // Update education service
            $education = $this->educationService->updateEducation($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'education' => $education,
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}