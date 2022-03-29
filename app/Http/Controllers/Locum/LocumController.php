<?php

namespace App\Http\Controllers\Locum;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Locum\AssignUserToLocumSessionRequest;
use App\Http\Requests\Locum\CreateLocumSessionRequest;
use App\Services\Locum\LocumService;

class LocumController extends Controller
{
    // Local variable
    protected $locumService;

    // Constructor
    public function __construct(LocumService $locumService)
    {
        // Inject Service
        $this->locumService = $locumService;
    }

    // Create session
    public function create(CreateLocumSessionRequest $request)
    {
        try {

            // Create locum session service
            return $this->locumService->createLocumSession($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Attach user to session
    public function assignUser(AssignUserToLocumSessionRequest $request)
    {
        try {

            // Assign user to session service
            return $this->locumService->addLocumToSession($request);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}