<?php

namespace App\Http\Controllers\InductionSchedule;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\InductionSchedule\CreateInductionScheduleRequest;
use App\Http\Requests\InductionSchedule\DeleteInductionScheduleRequest;
use App\Http\Requests\InductionSchedule\FetchInductionScheduleRequest;
use App\Models\InductionSchedule;
use App\Services\InductionSchedule\InductionScheduleService;

class InductionScheduleController extends Controller
{
    // Local variable
    protected $inductionScheduleService;

    // Constructor
    public function __construct(InductionScheduleService $inductionScheduleService)
    {
        // Inject service
        $this->inductionScheduleService = $inductionScheduleService;
    }

    // Create induction schedule
    public function create(CreateInductionScheduleRequest $request)
    {
        try {

            // Create induction schedule
            $inductionSchedule = $this->inductionScheduleService->createInductionSchedule($request);

            // Return success response
            return Response::success([
                'induction-schedule' => $inductionSchedule,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch induction schedules belonging to a practice
    public function fetch(FetchInductionScheduleRequest $request)
    {
        try {

            // Fetch induction schedule
            $inductionSchedules = $this->inductionScheduleService->fetchInductionSchedules($request);

            // Return success response
            return Response::success([
                'induction-schedules' => $inductionSchedules,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete induction schedule
    public function delete(DeleteInductionScheduleRequest $request)
    {
        try {

            // Delete induction schedule
            $this->inductionScheduleService->deleteInductionSchedule($request);

            // Return success response
            return Response::success([
                'message' => ResponseMessage::deleteSuccess('Induction Schedule'),
            ]);
        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

}