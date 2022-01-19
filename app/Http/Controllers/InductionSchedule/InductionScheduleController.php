<?php

namespace App\Http\Controllers\InductionSchedule;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\InductionSchedule\CreateInductionScheduleRequest;
use App\Http\Requests\InductionSchedule\DeleteInductionScheduleRequest;
use App\Http\Requests\InductionSchedule\FetchInductionScheduleRequest;
use App\Models\InductionSchedule;
use App\Models\Practice;
use App\Models\User;

class InductionScheduleController extends Controller
{
    // Create induction schedule
    public function create(CreateInductionScheduleRequest $request)
    {
        try {

            // Get Practice
            $practice = Practice::findOrFail($request->practice);

            // Get user
            $user = User::findOrFail($request->user);

            // Instance of InductionSchedule model
            $inductionSchedule = new InductionSchedule();
            $inductionSchedule->practice_id = $practice->id;
            $inductionSchedule->date = $request->date;
            $inductionSchedule->time = $request->time;
            $inductionSchedule->duration = $request->duration;
            $inductionSchedule->is_hq_required = $request->is_hq_required;
            $inductionSchedule->hq_staff_role_id = $request->is_hq_required ? $request->hq_staff_role_id : null;
            $inductionSchedule->hq_staff_id = $request->is_hq_required ? $request->hq_staff_id : null;
            $inductionSchedule->is_additional_staff_required = $request->is_additional_staff_required;
            $inductionSchedule->additional_staff_role_id = $request->is_additional_staff_required ? $request->additional_staff_role_id : null;
            $inductionSchedule->additional_staff_id = $request->is_additional_staff_required ? $request->additional_staff_id : null;

            // Check if the $user already has a schedule
            if ($user->inductionAlreadyScheduled()) {
                return Response::fail([
                    'code' => 409,
                    'message' => ResponseMessage::customMessage('User already has a schedule'),
                ]);
            }

            // Save induction Schedule
            $user->inductionSchedule()->save($inductionSchedule);

            // Save checklists related to induction schedule
            $inductionSchedule->inductionChecklists()->sync($this->mapChecklists($request->checklists));

            // Return success response
            return Response::success([
                'induction-schedule' => $inductionSchedule
                    ->with('user', 'practice', 'inductionChecklists.inductionQuestions')
                    ->first(),
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
            // Get practice
            $practice = Practice::findOrFail($request->practice);

            // Get induction schedules for the $practice
            $inductionSchedules = InductionSchedule::where('practice_id', $practice->id)
                ->with('user', 'practice', 'inductionChecklists.inductionQuestions')
                ->latest()
                ->paginate(10);

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
            // Get induction schedule
            $inductionSchedule = InductionSchedule::findOrFail($request->induction_schedule);

            // Delete induction schedule
            $inductionSchedule->delete();

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

    private function mapChecklists($checklists)
    {
        return collect($checklists)->map(function ($checklist) {
            return [
                'induction_checklist_id' => $checklist['induction_checklist_id'],
                'is_complete' => $checklist['is_complete'],
                'completed_date' => $checklist['completed_date'],
            ];
        });
    }
}