<?php

namespace App\Http\Controllers\CheckList;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Checklist\CreateChecklistRequest;
use App\Http\Requests\Checklist\FetchChecklistRequest;
use App\Services\Checklist\ChecklistService;
use Exception;

class CheckListController extends Controller
{
    // Local variable
    protected $checklistService;

    // Constructor
    public function __construct(ChecklistService $checklistService)
    {
        // Inject service
        $this->checklistService = $checklistService;
    }

    // Method for creating check list
    public function create(CreateChecklistRequest $request)
    {
        try {
            // Create checklist service
            $checklist = $this->checklistService->createChecklist($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_CREATED,
                'checklist' => $checklist,
            ]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function fetch(FetchChecklistRequest $request)
    {
        try {

            // Fetch checlists service
            $checklists = $this->checklistService->fetchChecklists($request);

            // Return success response
            return Response::success([
                'code' => Response::HTTP_CREATED,
                'checklists' => $checklists,
            ]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}