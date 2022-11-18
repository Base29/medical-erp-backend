<?php

namespace App\Http\Controllers\InductionSchedule;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\InductionSchedule\CreateInductionScheduleRequest;
use App\Http\Requests\InductionSchedule\DeleteInductionScheduleRequest;
use App\Http\Requests\InductionSchedule\FetchInductionScheduleRequest;
use App\Http\Requests\InductionSchedule\FetchSingleInductionRequest;
use App\Http\Requests\InductionSchedule\FetchUserInductionRequest;
use App\Http\Requests\InductionSchedule\UpdateInductionScheduleRequest;
use App\Services\InductionSchedule\InductionScheduleService;
use Exception;

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
                'code' => Response::HTTP_CREATED,
                'induction-schedule' => $inductionSchedule,
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
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
                'code' => Response::HTTP_OK,
                'induction-schedules' => $inductionSchedules,
            ]);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
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
                'code' => Response::HTTP_OK,
                'message' => ResponseMessage::deleteSuccess('Induction Schedule'),
            ]);
        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Single user induction
    public function userInduction(FetchUserInductionRequest $request)
    {
        try {
            // Logic here
            return $this->inductionScheduleService->fetchUserInduction($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Single Induction
    public function singleInduction(FetchSingleInductionRequest $request)
    {
        try {
            // Logic here
            return $this->inductionScheduleService->fetchSingleInduction($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update
    public function update(UpdateInductionScheduleRequest $request)
    {
        try {
            // Logic here
            return $this->inductionScheduleService->updateInductionSchedule($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

}