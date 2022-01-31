<?php

namespace App\Http\Controllers\PersonSpecification;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\PersonSpecification\CreatePersonSpecificationRequest;
use App\Http\Requests\PersonSpecification\DeletePersonSpecificationRequest;
use App\Http\Requests\PersonSpecification\FetchPersonSpecificationRequest;
use App\Services\PersonSpecification\PersonSpecificationService;

class PersonSpecificationController extends Controller
{
    // Local variable
    protected $personSpecificationService;

    // Constructor
    public function __construct(PersonSpecificationService $personSpecificationService)
    {
        // Inject service
        $this->personSpecificationService = $personSpecificationService;
    }

    // Create person specification
    public function create(CreatePersonSpecificationRequest $request)
    {
        try {

            // Create person specification
            return $this->personSpecificationService->createPersonSpecification($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch person specification
    public function fetch(FetchPersonSpecificationRequest $request)
    {
        try {

            // Fetch person specifications
            return $this->personSpecificationService->fetchPersonSpecifications($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete person specification
    public function delete(DeletePersonSpecificationRequest $request)
    {
        try {
            // Delete person specification
            return $this->personSpecificationService->deletePersonSpecification($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}