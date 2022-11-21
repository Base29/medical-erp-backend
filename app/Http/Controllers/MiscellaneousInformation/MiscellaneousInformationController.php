<?php

namespace App\Http\Controllers\MiscellaneousInformation;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\MiscellaneousInformation\CreateMiscellaneousInformationRequest;
use App\Http\Requests\MiscellaneousInformation\DeleteMiscellaneousInformationRequest;
use App\Http\Requests\MiscellaneousInformation\FetchMiscellaneousInformationRequest;
use App\Http\Requests\MiscellaneousInformation\UpdateMiscellaneousInformationRequest;
use App\Services\MiscInfo\MiscInfoService;
use Exception;

class MiscellaneousInformationController extends Controller
{
    // Local variable
    protected $miscInfoService;

    // Constructor
    public function __construct(MiscInfoService $miscInfoService)
    {
        // Inject service
        $this->miscInfoService = $miscInfoService;
    }

    // Create Miscellaneous Information
    public function create(CreateMiscellaneousInformationRequest $request)
    {
        try {

            // Create misc Info
            $miscInfo = $this->miscInfoService->createMiscInfo($request);

            return Response::success([
                'code' => Response::HTTP_CREATED,
                'misc-info' => $miscInfo,
            ]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch misc-info for a single user
    public function fetchSingle(FetchMiscellaneousInformationRequest $request)
    {
        try {

            // Fetch single misc info
            $miscInfo = $this->miscInfoService->fetchSingleMiscInfo($request);

            // Return response
            return Response::success([
                'code' => Response::HTTP_OK,
                'misc-info' => $miscInfo,
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete single misc-info
    public function delete(DeleteMiscellaneousInformationRequest $request)
    {
        try {

            // Delete misc info
            $this->miscInfoService->deleteMiscInfo($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'message' => ResponseMessage::deleteSuccess('Misc Info'),
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update misc info
    public function update(UpdateMiscellaneousInformationRequest $request)
    {
        try {

            // Update Misc Info
            $miscInfo = $this->miscInfoService->updateMiscInfo($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'misc-info' => $miscInfo,
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}