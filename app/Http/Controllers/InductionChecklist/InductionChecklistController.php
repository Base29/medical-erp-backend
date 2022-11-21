<?php

namespace App\Http\Controllers\InductionChecklist;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\InductionChecklist\CreateInductionChecklistRequest;
use App\Http\Requests\InductionChecklist\DeleteInductionChecklistRequest;
use App\Http\Requests\InductionChecklist\FetchInductionChecklistRequest;
use App\Http\Requests\InductionChecklist\FetchSingleInductionChecklistRequest;
use App\Http\Requests\InductionChecklist\UpdateInductionChecklistRequest;
use App\Services\InductionChecklist\InductionChecklistService;
use Exception;

class InductionChecklistController extends Controller
{

    // Local variable
    protected $inductionChecklistService;

    // Construct
    public function __construct(InductionChecklistService $inductionChecklistService)
    {
        // Inject service
        $this->inductionChecklistService = $inductionChecklistService;
    }

    // Create induction Checklist
    public function create(CreateInductionChecklistRequest $request)
    {
        try {
            // Create induction checklist
            $inductionChecklist = $this->inductionChecklistService->createInductionChecklist($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_CREATED,
                'induction-checklist' => $inductionChecklist,
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch induction checklists for a practice
    public function fetch(FetchInductionChecklistRequest $request)
    {
        try {

            // Fetch induction checklist
            $inductionChecklists = $this->inductionChecklistService->fetchInductionChecklists($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'induction-checklists' => $inductionChecklists,
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch single induction checklist
    public function fetchSingle(FetchSingleInductionChecklistRequest $request)
    {
        try {

            // Fetch single induction checklist
            $inductionChecklist = $this->inductionChecklistService->fetchSingleInductionChecklist($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'induction-checklist' => $inductionChecklist,
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete induction checklist
    public function delete(DeleteInductionChecklistRequest $request)
    {
        try {

            // Delete induction checklist
            $this->inductionChecklistService->deleteInductionChecklist($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'message' => ResponseMessage::deleteSuccess('Induction Checklist'),
            ]);
        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update induction checklist
    public function update(UpdateInductionChecklistRequest $request)
    {
        try {
            // Update induction checklist
            $inductionChecklist = $this->inductionChecklistService->updateInductionChecklist($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_OK,
                'induction-checklist' => $inductionChecklist,
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

}